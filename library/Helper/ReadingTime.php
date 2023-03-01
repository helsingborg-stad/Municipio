<?php

namespace Municipio\Helper;

class ReadingTime
{
    /**
     * It returns the reading time of a given content.
     *
     * @param string content The content you want to calculate the reading time for.
     * @param factor What factor to divide with, default 200 = normal reading speed
     *
     * @return int The estimated reading time of the content in minutes.
     */
    public static function getReadingTime(string $content = '', $factor = 200): int
    {
        return (int) ceil((str_word_count(strip_tags($content)) / $factor));
    }
}
