<?php

namespace Municipio\Helper;

interface StringToTimeInterface
{
    /**
     * Convert a string to a timestamp.
     *
     * @param string|int $string
     * @return ?int
     */
    public function convert(string|int $string): ?int;
}
