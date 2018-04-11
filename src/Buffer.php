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
        for ($i = $sourceStart, $j = $targetStart; $i <= $sourceEnd; $i++, $j++, $c++) {
            $target->setByteRaw($this->buffer[$i], $j);
        }

        return $c;

    }

    public function toString($start = 0, $end = null) {

        if (!is_int($end)) {
            $end = $this->length();
        }
        $buf = '';
        for ($i = $start; $i <= $end; $i++) {
            $buf .= $this->buffer[$i];
        }

        return $buf;
    }

    public static function isBuffer($buffer) {

        return $buffer instanceof Buffer;
    }

    /**
     * @param Buffer[] $buffers
     * @param null     $totalLength
     */
    public static function concat($list, $totalLength = null) {

        if (!isset($totalLength)) {
            foreach ($list as $buffer) {
                $totalLength += $buffer . length();
            }
        }

        $buf = new Buffer($totalLength);

        foreach ($list as $buffer) {
            foreach ($buffer as $byte) {
                if ($buf->length() >= $totalLength) {
                    return $buf;
                }
                $buf->setByteRaw($byte);
            }
        }

        return $buf;

    }

}