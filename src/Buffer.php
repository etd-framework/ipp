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
     * @param Buffer[] $buffers
     * @param null $totalLength
     */
    public static function concat($list, $totalLength = null) {

        if (!isset($totalLength)) {
            foreach ($list as $buffer) {
                $totalLength += $buffer.length();
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