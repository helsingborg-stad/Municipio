<?php

namespace Modularity\Helper;

use DateTime;
use DateTimeZone;
/**
 * Class Date
 * @package Modularity\Helper
 */
class Date
{
    /**
     * Returns format for date and time
     *
     * @param  string $format      A string that is either date or time or date-time
     * @return string              The format for date/time
     */
    public static function getDateFormat($format)
    {
        $defaultTime = 'H:i';
        $defaultDate = 'Y-m-d';
        $dateFormat = function_exists('get_option') && !empty(get_option('date_format')) ?
        get_option('date_format') : $defaultDate;

        $timeFormat = function_exists('get_option') && !empty(get_option('time_format')) ?
        get_option('time_format') : $defaultTime;

        if ($format === 'date') {
            return $dateFormat;
        } elseif ($format === 'time') {
            return $timeFormat;
        } elseif ($format === 'date-time') {
            return $dateFormat . ' ' . $timeFormat;
        }

        return $dateFormat . ' ' . $timeFormat;
    }
    /**
     * Returns a timestamp for a given date string
     *
     * @param  string $dateStr      A string that is a date
     * @return string               The timestamp for the date
     */
    public function getTimeStamp($dateStr) {
        $timezone = wp_timezone_string();
        $dateObj = new DateTime($dateStr, new DateTimeZone($timezone));
        return $dateObj->format('U');
    }
}
