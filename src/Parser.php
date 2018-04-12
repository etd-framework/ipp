<?php
/**
 * Part of the ETD Framework IPP Package
 *
 * @copyright   Copyright (C) 208 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions https://etd-solutions.com
 */

namespace EtdSolutions\IPP;

class Parser {

    const RS = "\u001e";

    protected $enums;

    protected $operations;

    protected $statusCodes;

    protected $tags;

    /**
     * @var Buffer
     */
    protected $buffer;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var array
     */
    protected $obj;

    protected $encoding = 'utf8';

    public function __construct() {

        $this->enums       = Data::get("enums");
        $this->operations  = Data::get("enums", "operations-supported");
        $this->statusCodes = Data::get("status");
        $this->tags        = Data::get("tags");

    }

    public function parse($buffer) {

        $this->buffer   = $buffer;
        $this->position = 0;
        $this->obj      = [];

        $this->obj["version"] = $this->read1() . '.' . $this->read1();
        $bytes2and3           = $this->read2();

        //byte[2] and byte[3] are used to define the 'operation' on
        //requests, but used to hold the statusCode on responses. We
        //can almost detect if it is a req or a res- but sadly, six
        //values overlap. In these cases, the parser will give both and
        //the consumer can ignore (or delete) whichever they don't want.

        if ($bytes2and3 >= 2 && $bytes2and3 <= 61) {
            $this->obj["operation"] = $this->operations["lookup"][$bytes2and3];
        }

        if ($bytes2and3 <= 7 || $bytes2and3 >= 1024) {
            $this->obj["statusCode"] = $this->statusCodes["lookup"][$bytes2and3];
        }

        $this->obj["id"] = $this->read4();
        $this->readGroups();

        if ($this->position < $this->buffer->length()) {
            $this->obj["data"] = $this->buffer->toString($this->encoding, $this->position);
        }

        return $this->obj;

    }

    protected function read1() {

        $val = $this->buffer->readInt8($this->position);
        $this->incrementPosition(1);

        return $val;
    }

    protected function read2() {

        $val = $this->buffer->readInt16BE($this->position);
        $this->incrementPosition(2);

        return $val;
    }

    protected function read4() {

        $val = $this->buffer->readInt32BE($this->position);
        $this->incrementPosition(4);

        return $val;
    }

    protected function read($length, $encoding = null) {

        if ($length == 0) {
            return '';
        }

        if (!isset($encoding)) {
            $encoding = $this->encoding;
        }

        $val = $this->buffer->toString($encoding, $this->position, $length);
        $this->incrementPosition($length);

        return $val;
    }

    protected function readGroups() {

        while ($this->position < $this->buffer->length() && ($group = $this->read1()) !== 0x03) { //end-of-attributes-tag
            $this->readGroup($group);
        }
    }

    protected function readGroup($group) {

        //echo "\n\nbegin readGroup($group)\n\n";

        if (isset($this->tags["lookup"][$group])) {

            $name = $this->tags["lookup"][$group];
            //echo "tag_name = $name\n";

            $grp = [];
            while (hexdec(bin2hex($this->buffer->getByteRaw($this->position))) >= 15) {// delimiters are between 0x00 to 0x0F
                $this->readAttr($grp);
            }

            if (isset($this->obj[$name])) {

                //echo "$name is set\n";

                if (!is_array($this->obj[$name])) {
                    $this->obj[$name] = [$this->obj[$name]];
                }

                $this->obj[$name][] = $grp;
            } else {
                $this->obj[$name] = $grp;
            }

        }

        //echo "\nend readGroup($group)\n\n";
        //print_r($this->obj);
        //if ($group == 4) die;
    }

    protected function readAttr(&$group) {

        //echo "\n begin readAttr()\n";

        $tag = $this->read1();

        /*echo "tag = $tag\n";
        echo "lookup = " . $this->tags["lookup"][$tag] . "\n";*/

        //@TODO: find a test for this
        if ($tag == $this->tags["extension"]) {//tags.extension
            $tag = $this->read4();
        }
        $name = $this->read($this->read2());
        //echo "name = $name\n";
        $group[$name] = $this->readValues($tag, $name);

        //echo "\n end readAttr()\n";

        return;
    }

    protected function hasAdditionalValue() {

        //echo "hasAdditionalValue()\n";

        $buf     = $this->buffer->getBufferArray();
        $current = bin2hex($this->buffer->getByteRaw($this->position));

        /*echo "current = $current\n";
        echo "#1 => " . ($current !== "4A" ? "YES" : "NO") . "\n";
        echo "#2 => " . ($current !== "37" ? "YES" : "NO") . "\n";
        echo "#3 => " . ($current !== "03" ? "YES" : "NO") . "\n";
        echo "#4 => " . (isset($buf[$this->position + 1]) && bin2hex($buf[$this->position + 1]) === "00" ? "YES" : "NO") . "\n";
        echo "#5 => " . (isset($buf[$this->position + 2]) && bin2hex($buf[$this->position + 2]) === "00" ? "YES" : "NO") . "\n";*/

        $res = $current !== "4A" // memberAttrName
            && $current !== "37" // endCollection
            && $current !== "03" // end-of-attributes-tag
            && isset($buf[$this->position + 1]) && bin2hex($buf[$this->position + 1]) === "00"
            && isset($buf[$this->position + 2]) && bin2hex($buf[$this->position + 2]) === "00";

        //echo "hasAdditionalValue = " . ($res ? "TRUE" : "FALSE") . "\n";

        return $res;
    }

    protected function readValues($type, $name = null) {

        //echo "begin readValues($type, $name)\n";

        $value = $this->readValue($type, $name);

        /*echo "value #1 = ";
        print_r($value);
        echo "\n";*/

        if ($this->hasAdditionalValue()) {
            //echo "hasAdditionalValue YES\n";
            $value = (array) $value;
            do {
                $type = $this->read1();
                //echo "tag = $type\n";
                $this->read2();//empty name
                $v = $this->readValue($type, $name);
                //echo "value IN = $v\n";
                $value[] = $v;
            } while ($this->hasAdditionalValue());
        }

        /*echo "value #2 = ";
        print_r($value);
        echo "\n";

        echo "end readValues($type, $name)\n";*/

        return $value;
    }

    protected function readValue($tag, $name = null) {

        /*echo "readValue($tag, $name)\n";
        echo "lookup = " . $this->tags["lookup"][$tag] . "\n";*/

        $length = $this->read2();
        //http://tools.ietf.org/html/rfc2910#section-3.9
        switch ($tag) {
            case $this->tags["enum"]:
                $val = $this->read4();
                if (isset($this->enums[$name]) && isset($this->enums[$name]["lookup"][$val])) {
                    return $this->enums[$name]["lookup"][$val];
                }

                return $val;
            case $this->tags["integer"]:
                return $this->read4();

            case $this->tags["boolean"]:
                return (bool) $this->read1();

            case $this->tags["rangeOfInteger"]:
                return [$this->read4(), $this->read4()];

            case $this->tags["resolution"]:
                return [$this->read4(), $this->read4(), $this->read1() === 0x03 ? 'dpi' : 'dpcm'];

            case $this->tags["dateTime"]:
                // http://tools.ietf.org/html/rfc1903 page 17
                $year  = $this->read2();
                $month = $this->read1();
                $day   = $this->read1();
                $hour  = $this->read1();
                $mins  = $this->read1();
                $secs  = $this->read1();
                $mili  = $this->read1();
                $tz_s  = $this->read(1);
                $this->read(1);
                $this->read(1);

                return "DATE";

            case $this->tags["textWithLanguage"]:
            case $this->tags["nameWithLanguage"]:
                $lang   = $this->read($this->read2());
                $subval = $this->read($this->read2());

                return $lang . self::RS . $subval;

            case $this->tags["nameWithoutLanguage"]:
            case $this->tags["textWithoutLanguage"]:
            case $this->tags["octetString"]:
            case $this->tags["memberAttrName"]:
                return $this->read($length);

            case $this->tags["keyword"]:
            case $this->tags["uri"]:
            case $this->tags["uriScheme"]:
            case $this->tags["charset"]:
            case $this->tags["naturalLanguage"]:
            case $this->tags["mimeMediaType"]:
                return $this->read($length, 'ascii');

            case $this->tags["begCollection"]:
                //the spec says a value could be present- but can be ignored
                $this->read($length);

                return $this->readCollection();

            case $this->tags["no-value"]:
            default:

                $value = $length ? $this->read($length) : null;

                echo("The spec is not clear on how to handle tag " . $tag . ": " . $name . "=" . ((string) $value) . ". Please open a github issue to help find a solution!\n");

                return $value;

        }
    }

    protected function readCollection() {

        //echo "begin readCollection()\n";

        $collection = [];

        while (($tag = $this->read1()) !== $this->tags["endCollection"] ) { //tags.endCollection
            if ($tag !== $this->tags["memberAttrName"]) {
                echo("unexpected:" . $this->tags["lookup"][$tag]);

                return $collection;
            }
            //read nametag name and discard it
            $this->read($this->read2());
            $name              = $this->readValue(0x4A);
            //echo "name = $name\n";
            $values            = $this->readCollectionItemValue();
            /*echo "values = ";
            print_r($values);
            echo "\n";*/
            $collection[$name] = $values;
        }
        //Read endCollection name & value and discard it.
        //The spec says that they MAY have contents in the
        // future- so we can't assume they are empty.
        $this->read($this->read2());
        $this->read($this->read2());

        return $collection;
    }

    protected function readCollectionItemValue($name = null) {

        //echo "begin readCollectionItemValue($name)\n";

        $tag = $this->read1();

        //echo "tag = $tag\n";

        //TODO: find a test for this
        if ($tag === $this->tags["extension"]) {//tags.extension
            $tag = $this->read4();
        }
        //read valuetag name and discard it
        $this->read($this->read2());

        return $this->readValues($tag, $name);
    }

    protected function incrementPosition($val) {

        if ($this->position + $val > $this->buffer->length()) {
            $d = debug_backtrace();
            foreach ($d as $i => $a) {
                echo "#$i " . $a["file"] . ":" . $a["line"] . " " . get_class($a["object"]) . $a["type"] . $a["function"] . "()\n";
            }
        }
        $this->position += $val;
    }

}