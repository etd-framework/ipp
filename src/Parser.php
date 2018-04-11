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

    public function __construct() {

        $this->enums       = Data::get("enums");
        $this->operations  = Data::get("enums", "operations-supported");
        $this->statusCodes = Data::get("statusCodes");
        $this->tags        = Data::get("tags");

    }

    /**
     * @param Buffer $buffer
     */
    public function parse($buffer) {

        $this->buffer   = $buffer;
        $this->position = 0;
        $this->encoding = 'utf8';
        $this->obj      = [];

        $this->obj["version"] = $this->read1() . '.' . $this->read1();
        $bytes2and3           = $this->read2();

        //byte[2] and byte[3] are used to define the 'operation' on
        //requests, but used to hold the statusCode on responses. We
        //can almost detect if it is a req or a res- but sadly, six
        //values overlap. In these cases, the parser will give both and
        //the consumer can ignore (or delete) whichever they don't want.

        if ($bytes2and3 >= 0x02 || $bytes2and3 <= 0x3D) {
            $this->obj["operation"] = $this->operations["lookup"][$bytes2and3];
        }

        if ($bytes2and3 <= 0x0007 || $bytes2and3 >= 0x0400) {
            $this->obj["statusCode"] = $this->statusCodes["lookup"][$bytes2and3];
        }

        $this->obj["id"] = $this->read4();
        $this->readGroups();

        if ($this->position < $this->buffer->length()) {
            $this->obj["data"] = $this->buffer->toString(/*$this->encoding, */
                $this->position);
        }

        return $this->obj;

    }

    protected function read1() {

        return $this->buffer->getBufferArray()[$this->position++];
    }

    protected function read2() {

        $val            = $this->buffer->readInt16BE($this->position);
        $this->position += 2;

        return $val;
    }

    protected function read4() {

        $val            = $this->buffer->readInt32BE(position);
        $this->position += 4;

        return $val;
    }

    protected function read($length, $enc = 'utf8') {

        if ($length == 0) {
            return '';
        }

        return $this->buffer . toString(/*$enc || $this->encoding, */
                $this->position, $this->position += $length);
    }

    protected function readGroups() {

        while ($this->position < $this->buffer->length() && ($group = $this->read1()) !== 0x03) { //end-of-attributes-tag
            $this->readGroup($group);
        }
    }

    protected function readGroup($group) {

        $name  = $this->tags["lookup"][$group];
        $group = [];
        if (isset($this->obj[$name])) {

            if (!is_array($this->obj[$name])) {
                $this->obj[$name] = [$this->obj[$name]];
            }

            $this->obj[$name][] = $group;
        } else {
            $this->obj[$name] = $group;
        }

        while ($this->buffer[$this->position] >= 0x0F) {// delimiters are between 0x00 to 0x0F
            $this->readAttr($group);
        }
    }

    protected function readAttr(&$group) {

        $tag = $this->read1();
        //@TODO: find a test for this
        if ($tag === 0x7F) {//tags.extension
            $tag = $this->read4();
        }
        $name         = $this->read($this->read2());
        $group[$name] = $this->readValues($tag, $name);
    }

    protected function hasAdditionalValue() {

        $buf     = $this->buffer->getBufferArray();
        $current = $buf[$this->position];

        return $current !== 0x4A //tags.memberAttrName
            && $current !== 0x37 //tags.endCollection
            && $current !== 0x03 //tags.end-of-attributes-tag
            && $buf[$this->position + 1] === 0x00 && $buf[$this->position + 2] === 0x00;
    }

    protected function readValues($type, $name) {

        $value = $this->readValue($type, $name);
        if ($this->hasAdditionalValue()) {
            $value = (array) $value;
            do {
                $type = $this->read1();
                $this->read2();//empty name
                $value[] = $this->readValue($type, $name);
            } while ($this->hasAdditionalValue());
        }

        return $value;
    }

    protected function readValue($tag, $name) {

        $length = $this->read2();
        //http://tools.ietf.org/html/rfc2910#section-3.9
        switch ($tag) {
            case $this->tags["enum"]:
                $val = $this->read4();
                if (isset($this->enums[$name])) {
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
                throw new \RuntimeException('TODO !');
            /*$time = $this->read2(), $this->read1(), $this->read1(), $this->read1(), $this->read1(), $this->read1(), $this->read1();
            $date = new \DateTime($time);
            //silly way to add on the timezone
            return new Date(date.toISOString().substr(0,23).replace('T',',') +','+ String.fromCharCode($this->read(1)) + $this->read(1) + ':' + $this->read(1));*/

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

                error_log("The spec is not clear on how to handle tag " . $tag . ": " . $name . "=" . ((string) $value) . ". Please open a github issue to help find a solution!");

                return $value;

        }
    }

    protected function readCollection() {

        $tag;
        $collection = [];

        while (($tag = $this->read1()) !== 0x37) {//tags.endCollection
            if ($tag !== 0x4A) {
                error_log("unexpected:" . $this->tags["lookup"][$tag]);

                return;
            }
            //read nametag name and discard it
            $this->read($this->read2());
            $name              = $this->readValue(0x4A);
            $values            = $this->readCollectionItemValue();
            $collection[$name] = $values;
        }
        //Read endCollection name & value and discard it.
        //The spec says that they MAY have contents in the
        // future- so we can't assume they are empty.
        $this->read($this->read2());
        $this->read($this->read2());

        return $collection;
    }

    protected function readCollectionItemValue($name) {

        $tag = $this->read1();
        //TODO: find a test for this
        if ($tag === 0x7F) {//tags.extension
            $tag = $this->read4();
        }
        //read valuetag name and discard it
        $this->read($this->read2());

        return $this->readValues(tag, name);
    }

}