<?php
/**
 * Part of the ETD Framework IPP Package
 *
 * @copyright   Copyright (C) 208 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions https://etd-solutions.com
 */

namespace EtdSolutions\IPP;

use t3ran13\ByteBuffer\ByteBuffer;

class Buffer extends ByteBuffer {

    public function getByteRaw($offset = null) {
        if ($offset === null) {
            $offset = $this->currentOffset;
        }
        if (!isset($this->buffer[$offset])) {
            return false;
        }
        return $this->buffer[$offset];
    }

    public function write($string, $offset = null, $format = 'a', $length = null) {

        if (!isset($length)) {
            $length = strlen($string);
        }
        $this->insert($format . $length, $string, $offset, $length);
    }

    public function read($offset, $length, $format = 'a') {
        return $this->extract($format. $length, $offset, $length);
    }

    /**
     * @param int $start
     * @param int $end
     *
     * @return Buffer
     */
    public function slice($start = 0, $end = null) {

        if (!isset($end)) {
            $end = $this->length();
        }

        $new = new Buffer($this->length());
        for ($i = $start; $i <= $end; $i++) {
            $new->setByteRaw($this->buffer[$i]);
        }

        return $new;

    }

    /**
     * @param Buffer $target      A Buffer to copy into.
     * @param int    $targetStart The offset within target at which to begin copying to. Default: 0
     * @param int    $sourceStart The offset within buf at which to begin copying from. Default: 0
     * @param int    $sourceEnd   The offset within buf at which to stop copying (not inclusive). Default: buf.length
     *
     * @return int The number of bytes copied.
     */
    public function copy($target, $targetStart = 0, $sourceStart = 0, $sourceEnd = null) {

        if (!isset($sourceEnd)) {
            $sourceEnd = $this->length();
        }

        $c = 0;
        for ($i = $sourceStart, $j = $targetStart; $i < $sourceEnd; $i++, $j++, $c++) {
            $target->setByteRaw($this->buffer[$i], $j);
        }

        return $c;

    }

    public function toString($encoding = 'utf8', $start = 0, $end = null) {

        if (!is_int($end)) {
            $end = $this->length();
        }

        $format = 'a';

        if ($encoding == 'hex') {
            $format = 'H';
        }

        $data = $this->getBuffer($format, $start, $end);

        if ($encoding == 'utf8') {
            $data = utf8_decode($data);
        } elseif ($encoding == 'ascii') {
            $data = mb_convert_encoding($data, "ASCII");
        }

        return $data;
    }

    public static function isBuffer($buffer) {

        return $buffer instanceof Buffer;
    }

    /**
     * @param Buffer|Buffer[] $list
     * @param null     $totalLength
     */
    public static function concat($list, $totalLength = null) {

        $list = (array) $list;

        if (!isset($totalLength)) {
            foreach ($list as $buffer) {
                $totalLength += $buffer->length();
            }
        }

        $buf = new Buffer($totalLength);

        $c = 0;
        foreach ($list as $buffer) {
            if (is_string($buffer)) {
                $buffer = str_split($buffer);
            }
            foreach ($buffer as $byte) {
                if ($c >= $totalLength) {
                    return $buf;
                }
                $buf->setByteRaw($byte);
                $c++;
            }
        }

        return $buf;

    }

    public static function from($string, $encoding = 'utf8') {

        $length        = strlen($string);
        $format        = 'a';
        $format_length = $length;

        if ($encoding == 'hex') {
            $format        = 'H';
            $format_length = '*';
        } elseif ($encoding == 'utf8') {
            $string = utf8_encode($string);
        } elseif ($encoding == 'ascii') {
            $string = mb_convert_encoding($string, "ASCII");
        }

        $buffer = new Buffer($length);
        $buffer->write($string, 0, $format, $format_length);

        return $buffer;

    }

}