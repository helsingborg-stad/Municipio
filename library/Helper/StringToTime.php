<?php

namespace Municipio\Helper;

use WpService\Contracts\__;

/**
 * StringToTime class.
 */
class StringToTime implements StringToTimeInterface
{
    /**
     * StringToTime constructor.
     */
    public function __construct(private __ $wpService)
    {
    }
    /**
     * Try to format a date to a unix timestamp
     *
     * @param mixed $date The date to format
     * @return int|null The formatted date as a unix timestamp or null if the date could not be formatted
     */
    public function convert(string|int $date): ?int
    {
        if (is_int($date)) {
            return $date;
        }

        $dateStringReplaceMap = $this->getLiteralDateStringReplaceMap();
        $date                 = str_ireplace($dateStringReplaceMap[0], $dateStringReplaceMap[1], $date);

        return strtotime($date) ?: null;
    }

    /**
     * Get the literal date string replace map
     * @return array
     */
    private function getLiteralDateStringReplaceMap(): array
    {
        $literals        = [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ];
        $literalsShort   = array_map(fn($literal) => substr($literal, 0, 3), $literals);
        $translated      = array_map(fn($literal) => $this->wpService->__($literal), $literals);
        $translatedShort = array_map(fn($literal) => $this->wpService->__(substr($literal, 0, 3)), $literals);

        $search  = array_merge($translated, $translatedShort);
        $replace = array_merge($literals, $literalsShort);

        return [$search, $replace];
    }
}
