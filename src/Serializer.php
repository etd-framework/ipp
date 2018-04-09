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

    const RS = '\u001e';

    protected $operations;

    protected $tags;

    protected $versions;

    protected $attributes;

    protected $enums;

    protected $keywords;

    protected $statusCodes;

    public function __construct() {

        $this->operations  = Data::get("enums", "operations-supported");
        $this->tags        = Data::get("tags");
        $this->versions    = Data::get("versions");
        $this->attributes  = Data::get("attributes");
        $this->enums       = Data::get("enums");
        $this->keywords    = Data::get("keywords");
        $this->statusCodes = Data::get("statusCodes");

    }

    public function serialize($msg) {

        $buf      = new Buffer(10240);
        $position = 0;

        function checkBufferSize($length) use ($position, $buf) {

            if ($position + $length > $buf.length()) {
                $buf = Buffer::concat([$buf], 2 * $buf.length());
            }
        }

        function write1($val) use ($buf, $position) {

            checkBufferSize(1);
            $buf->writeUInt8($val, $position);
            $position += 1;
        }

        function write2($val) use ($buf, $position) {

            checkBufferSize(2);
            $buf->writeUInt16BE($val, $position);
            $position += 2;
        }

        function write4($val) use ($buf, $position) {

            checkBufferSize(4);
            $buf->writeUInt32BE($val, $position);
            $position += 4;
        }

        function write($str, $enc = null) use ($buf, $position) {

            $length = strlen($str);
            write2($length);
            checkBufferSize($length);
            $buf->write($str, $position);
            $position += $length;
        }



    }

    protected function random() {

        $int = (float) mt_rand() / (float ) mt_getrandmax();

        return substr((string) $int, -8);
    }

}