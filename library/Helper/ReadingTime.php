<?php

namespace Municipio\Helper;

use Municipio\PostObject\PostObjectInterface;

/**
 * Class ListingTest
 */
class ReadingTime
{
    /**
     * Calculates the reading time for a given content.
     *
     * @param string $content The content to calculate the reading time for. Default is an empty string.
     * @param int $factor The factor to use for calculating the reading time. Default is 200.
     * @param bool $i18n Whether or not to internationalize the reading time. Default is false.
     *
     * @return int|string The reading time, in minutes, or a localized string indicating less than a minute,
     * depending on the $i18n parameter.
     * */
    public static function getReadingTime(string $content = '', int $factor = 200, bool $i18n = false)
    {
        if (0 === $factor) {
            $factor = 200;
        }

        $wordsCount  = str_word_count(strip_tags($content));
        $readingTime = (int) ceil(($wordsCount / $factor));

        if ($i18n) {
            if ($wordsCount < $factor) {
                return __('Less than a minute', 'municipio');
            }
            return sprintf(_n('1 minute', '%d minutes', $readingTime, 'municipio'), $readingTime);
        }

        return $readingTime;
    }

    public static function getReadingTimeFromPostObject(PostObjectInterface $postObject): int|string
    {
        return self::getReadingTime($postObject->getContent());
    }
}
