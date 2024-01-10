<?php

namespace Municipio\Helper;

/*
 * Returns format for date and time
 */
class DateFormat
{
    /**
     * Get the date format based on the specified format type.
     *
     * @param string $format The format type ('date', 'time', or 'date-time').
     *
     * @return string The corresponding date format string.
     */
    public static function getDateFormat(string $format = 'date-time'): string
    {
        $defaultTime = 'H:i';
        $defaultDate = 'Y-m-d';

        $dateFormat = !empty(get_option('date_format')) ? get_option('date_format') : $defaultDate;
        $timeFormat = !empty(get_option('time_format')) ? get_option('time_format') : $defaultTime;

        $returnFormat = $dateFormat . ' ' . $timeFormat;

        switch ($format) {
            case 'date':
                return $dateFormat;
            case 'time':
                return $timeFormat;
            case 'date-time':
            default:
                return $dateFormat . ' ' . $timeFormat;
        }
    }
}
