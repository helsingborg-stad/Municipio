<?php

namespace Municipio\Helper;

class StringToTime
{
    /**
     * Try to format a date to a unix timestamp
     *
     * @param mixed $date The date to format
     * @return int|null The formatted date as a unix timestamp or null if the date could not be formatted
     */
    public static function convert(string|int $date): ?int
    {
        if (is_int($date)) {
            return $date;
        }

        $date = str_ireplace(self::getLiteralDateStringReplaceMap()[0], self::getLiteralDateStringReplaceMap()[1], $date);

        return strtotime($date) ?: null;
    }

    /**
     * Get the literal date string replace map
     * @return array
     */
    private static function getLiteralDateStringReplaceMap(): array
    {
        $wpService       = \Municipio\Helper\WpService::get();
        $literals        = [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ];
        $literalsShort   = array_map(fn($literal) => substr($literal, 0, 3), $literals);
        $translated      = array_map(fn($literal) => $wpService->__($literal), $literals);
        $translatedShort = array_map(fn($literal) => $wpService->__(substr($literal, 0, 3)), $literals);

        $search  = array_merge($translated, $translatedShort);
        $replace = array_merge($literals, $literalsShort);

        return [$search, $replace];
    }
}