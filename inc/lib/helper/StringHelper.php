<?php

if (!class_exists('StringHelper')) {
    class StringHelper
    {
        public static function escape($value): string
        {
            return TextHelper::escape($value);
        }

        public static function unescape($value): string
        {
            return TextHelper::decode($value);
        }
    }
}
