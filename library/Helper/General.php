<?php

namespace Municipio\Helper;

class General
{
    /**
     * Wraps a string in a <span> tag with optional attributes.
     *
     * @param string $string The string to wrap.
     * @param array $attributes An array of attributes to add to the <span> tag.
     * @return string The wrapped string.
     */
    public static function wrapStringInSpan(string $string = '', array $attributes = []): string
    {
        if ('' === $string) {
            return '';
        }

        if (!empty($attributes)) {
            $attributes = implode(' ', array_map(fn($key, $value) => "$key=\"$value\"", array_keys($attributes), $attributes));
            return "<span $attributes>$string</span>";
        } else {
            return "<span>$string</span>";
        }
    }
}
