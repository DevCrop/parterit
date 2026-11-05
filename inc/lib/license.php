<?php
/*
 * Legacy API compatibility only. Customer-hosted deployment does not enforce
 * a hostname license; no development or vendor host registrations are shipped.
 */


class Lisence
{
    static $keys = [];

    static function add($newLisence = '')
    {
        if (isset($newLisence) && !empty($newLisence)) {
            self::$keys[] = $newLisence;
        }
    }

    static function getAll()
    {
        return self::$keys;
    }
}
