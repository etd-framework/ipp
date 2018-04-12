<?php
/**
 * Part of the ETD Framework IPP Package
 *
 * @copyright   Copyright (C) 208 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions https://etd-solutions.com
 */

namespace EtdSolutions\IPP;

class Util {

    /**
     * To serialize and deserialize, we need to be able to look
     * things up by key or by value. This little helper just
     * converts the arrays to objects and tacks on a 'lookup' property.
     *
     * @param array $a
     */
    public static function xref(array $a) {

        $obj = [];

        foreach ($a as $k => $v) {
            $obj[$v] = $k;
        }

        $obj["lookup"] = $a;

        return $obj;

    }

    public static function array_some(array $a, callable $cb) {

        foreach ($a as $elem) {
            if (call_user_func($cb, $a)) {
                return true;
            }
        }

        return false;

    }

    public static function random() {

        $int = (float) mt_rand() / (float ) mt_getrandmax();

        return substr((string) $int, -8);
    }

}