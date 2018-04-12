<?php
/**
 * Part of the ETD Framework IPP Package
 *
 * @copyright   Copyright (C) 208 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions https://etd-solutions.com
 */

namespace EtdSolutions\IPP;

class Serializer {

    const DEFAULT_VERSION = "2.0";
    const RS              = "\u001e";
    const BUFFER_SIZE     = 10240;
    const SPECIAL         = [
        "attributes-charset"          => 1,
        "attributes-natural-language" => 2
    ];
    const GROUPMAP        = [
        "job-attributes-tag"                => ["Job Template", "Job Description"],
        "operation-attributes-tag"          => "Operation",
        "printer-attributes-tag"            => "Printer Description",
        "unsupported-attributes-tag"        => "",//??
        "subscription-attributes-tag"       => "Subscription Description",
        "event-notification-attributes-tag" => "Event Notifications",
        "resource-attributes-tag"           => "",//??
        "document-attributes-tag"           => "Document Description"
    ];

    protected $operations;

    protected $tags;

    protected $versions;

    protected $attributes;

    protected $enums;

    protected $keywords;

    protected $statusCodes;

    /**
     * @var Buffer
     */
    protected $buffer;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var string
     */
    protected $msg;

    public function __construct() {

        $this->operations  = Data::get("enums", "operations-supported");
        $this->tags        = Data::get("tags");
        $this->versions    = Data::get("versions");
        $this->attributes  = Data::get("attributes");
        $this->enums       = Data::get("enums");
        $this->keywords    = Data::get("keywords");
        $this->statusCodes = Data::get("status");

    }

    /**
     * @param array $msg
     * @return Buffer
     */
    public function serialize($msg) {

        $this->msg      = $msg;
        $this->buffer   = new Buffer(self::BUFFER_SIZE);
        $this->position = 0;

        $this->write2($this->versions[isset($this->msg["version"]) ? $this->msg["version"] : self::DEFAULT_VERSION]);
        $this->write2(isset($this->msg["operation"]) ? $this->operations[$this->msg["operation"]] : $this->statusCodes[$this->msg["statusCode"]]);
        $this->write4(isset($this->msg["id"]) ? $this->msg["id"] : Util::random()); // request-id

        $this->writeGroup('operation-attributes-tag');
        $this->writeGroup('job-attributes-tag');
        $this->writeGroup('printer-attributes-tag');
        $this->writeGroup('document-attributes-tag');
        //@TODO add the others

        $this->write1(0x03); //end

        if (!isset($msg["data"])) {
            return $this->buffer->slice(0, $this->position);
        }

        if (!Buffer::isBuffer($msg["data"])) {
            throw new \RuntimeException("data must be a Buffer");
        }

	    $buf2 = new Buffer($this->position + $msg["data"]->length());
	    $this->buffer->copy($buf2, 0, 0, $this->position);
        $msg["data"]->copy($buf2, $this->position, 0);

        return $buf2;

    }

    protected function checkBufferSize($length) {

        if ($this->position + $length > $this->buffer->length()) {
            $this->buffer = Buffer::concat([$this->buffer], 2 * $this->buffer->length());
        }
    }

    protected function write1($val) {

        $this->checkBufferSize(1);
        $this->buffer->writeInt8($val, $this->position);
        $this->position += 1;
    }

    protected function write2($val) {

        $this->checkBufferSize(2);
        $this->buffer->writeInt16BE($val, $this->position);
        $this->position += 2;
    }

    protected function write4($val) {

        $this->checkBufferSize(4);
        $this->buffer->writeInt32BE($val, $this->position);
        $this->position += 4;
    }

    protected function write($str, $encoding = null) {

        if (!isset($encoding)) {
            $encoding = 'utf8';
        }

        if ($encoding == 'utf8') {
            $str = utf8_encode($str);
        } elseif ($encoding == 'ascii') {
            $str = mb_convert_encoding($str, "ASCII");
        }

        $length = strlen($str);
        $this->write2($length);
        $this->checkBufferSize($length);
        $this->buffer->write($str, $this->position);
        $this->position += $length;
    }

    protected function writeGroup($tag) {

        if (!isset($this->msg[$tag])) {
            return;
        }

        $attrs = $this->msg[$tag];
        $keys = array_keys($attrs);

        //'attributes-charset' and 'attributes-natural-language' need to come first- so we sort them to the front
        if ($tag == 'operation-attributes-tag') {
            usort($keys, function ($a, $b) {

                return (isset(self::SPECIAL[$a]) ? self::SPECIAL[$a] : 3) - (isset(self::SPECIAL[$b]) ? self::SPECIAL[$b] : 3);
            });
        }

        $groupname = self::GROUPMAP[$tag];
        $this->write1($this->tags[$tag]);

        foreach ($keys as $name) {
            $this->attr($groupname, $name, $attrs);
        }
    }

    protected function attr($group, $name, $obj) {

        $groupName = null;
        if (is_array($group)) {
            foreach ($group as $grp) {
                if (isset($this->attributes[$grp][$name])) {
                    $groupName = $grp;
                    break;
                }
            }
        } else {
            $groupName = $group;
        }

        if (!isset($groupName)) {
            throw new \RuntimeException("Unknown attribute: " . $name);
        }

        if (!isset($this->attributes[$groupName][$name])) {
            throw new \RuntimeException("Unknown attribute: " . $name);
        }

        $syntax = $this->attributes[$groupName][$name];
        $value  = (array) $obj[$name];

        foreach ($value as $i => $v) {

            //we need to re-evaluate the alternates every time
            $syntax2 = is_array($syntax) && isset($syntax["setof"]) ? $this->resolveAlternates($syntax, $name, $v) : $syntax;
            $tag     = $this->getTag($syntax2, $name, $v);

            if ($tag === $this->tags["enum"]) {
                $v = $this->enums[$name][$v];
            }

            $this->write1($tag);
            if ($i == 0) {
                $this->write($name);
            } else {
                $this->write2(0x0000); //empty name
            }

            $this->writeValue($tag, $v, isset($syntax2["members"]) ? $syntax2["members"] : null);

        }
    }

    protected function getTag($syntax, $name, $value) {

        if (isset($syntax["tag"])) {
            return $syntax["tag"];
        }

        $hasRS = strpos($value, self::RS);

        return $this->tags[$syntax["type"] . ($hasRS !== false ? "With" : "Without") . "Language"];
    }

    protected function resolveAlternates($array, $name, $value) {

        if (!isset($array["alts"])) {
            throw new \RuntimeException("Unknown atlernates");
        }

        switch ($array["alts"]) {
            case 'keyword,name':
            case 'keyword,name,novalue':

                if (!isset($value) && $array["lookup"]["novalue"]) {
                    return $array["lookup"]['novalue'];
                }

                return in_array($this->keywords[$name], $value) ? $array["lookup"]["keyword"] : $array["lookup"]["name"];

            case 'integer,rangeOfInteger':

                return is_array($value) ? $array["lookup"]["rangeOfInteger"] : $array["lookup"]["integer"];

            case 'dateTime,novalue':

                return date_parse($value) !== false ? $array["lookup"]["dateTime"] : $array["lookup"]["novalue"];

            case 'integer,novalue':

                return is_numeric($value) ? $array["lookup"]["integer"] : $array["lookup"]["novalue"];

            case 'name,novalue':

                return isset($value) ? $array["lookup"]["name"] : $array["lookup"]["novalue"];

            case 'novalue,uri':

                return isset($value) ? $array["lookup"]["uri"] : $array["lookup"]["novalue"];

            case 'enumeration,unknown':

                return isset($this->enums[$name][$value]) ? $array["lookup"]["enumeration"] : $array["lookup"]["unknown"];

            case 'enumeration,novalue':

                return isset($value) ? $array["lookup"]["enumeration"] : $array["lookup"]["novalue"];

            case 'collection,novalue':

                return isset($value) ? $array["lookup"]["enumeration"] : $array["lookup"]["novalue"];

            default:
                throw new \RuntimeException("Unknown atlernates");
        }
    }

    protected function writeValue($tag, $value, $submembers = null) {

        switch ($tag) {
            case $this->tags["enum"]:
                $this->write2(0x0004);
                $this->write4($value);

                return;

            case $this->tags["integer"]:
                $this->write2(0x0004);
                $this->write4($value);

                return;

            case $this->tags["boolean"]:
                $this->write2(0x0001);
                $this->write1((int) ($value));

                return;

            case $this->tags["rangeOfInteger"]:
                $this->write2(0x0008);
                $this->write4($value[0]);
                $this->write4($value[1]);

                return;

            case $this->tags["resolution"]:
                $this->write2(0x0009);
                $this->write4($value[0]);
                $this->write4($value[1]);
                $this->write1($value[2] === 'dpi' ? 0x03 : 0x04);

                return;

            case $this->tags["dateTime"]:
                $this->write2(0x000B);
                $this->write2((int) $value->format('Y'));
                $this->write1((int) $value->format('m'));
                $this->write1((int) $value->format('d'));
                $this->write1((int) $value->format('H'));
                $this->write1((int) $value->format('i'));
                $this->write1((int) $value->format('s'));
                $this->write1((int) $value->format('v'));
                $tz = $value->format('O');
                $this->write1(substr($tz, 0, 1)); // + or -
                $this->write1(substr($tz, 1, 2)); // hours
                $this->write1(substr($tz, 3, 2)); // minutes

                return;

            case $this->tags["textWithLanguage"]:
            case $this->tags["nameWithLanguage"]:
                /*$this->write2(parts[0] . length);
                $this->write2(parts[0]);
                $this->write2(parts[1] . length);
                $this->write2(parts[1]);*/

                return;

            case $this->tags["nameWithoutLanguage"]:
            case $this->tags["textWithoutLanguage"]:
            case $this->tags["octetString"]:
            case $this->tags["memberAttrName"]:
                return $this->write($value);

            case $this->tags["keyword"]:
            case $this->tags["uri"]:
            case $this->tags["uriScheme"]:
            case $this->tags["charset"]:
            case $this->tags["naturalLanguage"]:
            case $this->tags["mimeMediaType"]:
                return $this->write($value, 'ascii');

            case $this->tags["begCollection"]:
                $this->write2(0);//empty value

                return $this->writeCollection($value, $submembers);

            case $this->tags["no-value"]:
                //empty value? I can't find where this is defined in any spec.
                return $this->write2(0);

            default:
                throw new \RuntimeException("not handled : " . $tag);
        }
    }

    protected function writeCollection($value, $members) {

        foreach (array_keys($value) as $key) {

            $subvalue  = $value[$key];
            $subsyntax = $members[$key];

            if (is_array($subsyntax) && isset($subsyntax["setof"])) {
                $subsyntax = $this->resolveAlternates($subsyntax, $key, $subvalue);
            }

            $tag = $this->getTag($subsyntax, $key, $subvalue);

            if ($tag === $this->tags["enum"]) {
                $subvalue = $this->enums[$key][$subvalue];
            }

            $this->write1($this->tags["memberAttrName"]);
            $this->write2(0);//empty name
            $this->writeValue($this->tags["memberAttrName"], $key);
            $this->write1($tag);
            $this->write2(0);//empty name
            $this->writeValue($tag, $subvalue, $subsyntax["members"]);
        }

        $this->write1($this->tags["endCollection"]);
        $this->write2(0); //empty name
        $this->write2(0); //empty value
    }

}