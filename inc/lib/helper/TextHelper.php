<?php

final class TextHelper
{
    public static function escape($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', false);
    }

    public static function decode($value): string
    {
        return htmlspecialchars_decode((string) $value, ENT_QUOTES);
    }

    public static function nl2brEscaped($value): string
    {
        $text = (string) $value;
        $hasBr = preg_match('/<br\s*\/?>/i', $text) === 1;
        $token = '__NB_BR__';

        $text = preg_replace('/\s*<br\s*\/?>\s*/i', $token, $text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        if ($hasBr) {
            $text = str_replace("\n", ' ', $text);
            $text = preg_replace('/\s+/', ' ', $text);
        } else {
            $text = preg_replace("/\n{2,}/", "\n", $text);
        }

        $text = str_replace($token, "\n", $text);
        $text = trim($text);

        return nl2br(self::escape($text));
    }

    public static function nl2brEscapedHtml($value): string
    {
        $text = (string) $value;
        $tags = [];
        $text = preg_replace_callback(
            '#</?(?:strong|b)\b[^>]*>|<br\s*/?>#i',
            static function (array $matches) use (&$tags): string {
                $raw = $matches[0];
                if (preg_match('#^<br\s*/?>#i', $raw)) {
                    $tags[] = '<br>';
                } elseif (preg_match('#^</(strong|b)>#i', $raw, $tagMatches)) {
                    $tags[] = '</' . strtolower($tagMatches[1]) . '>';
                } elseif (preg_match('#^<(strong|b)\b[^>]*>#i', $raw, $tagMatches)) {
                    $tags[] = '<' . strtolower($tagMatches[1]) . '>';
                } else {
                    $tags[] = '';
                }

                return '__EC_H_' . (count($tags) - 1) . '__';
            },
            $text
        );

        $output = self::nl2brEscaped($text);
        foreach ($tags as $index => $tag) {
            $output = str_replace('__EC_H_' . $index . '__', $tag !== '' ? $tag : '', $output);
        }

        return $output;
    }
}
