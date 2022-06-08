<?php

namespace Municipio\Helper;
/*
 * Returns format for date and time
 */
class DateFormat
{
    public static function getDateFormat($format)
    {
        $defaultTime = 'H:i';
        $defaultDate = 'Y-m-d';
        $dateFormat = function_exists('get_option') && !empty(get_option('date_format')) ? get_option('date_format') : $defaultDate;
        $timeFormat = function_exists('get_option') && !empty(get_option('time_format')) ? get_option('time_format') : $defaultTime;

        $returnFormat = $dateFormat . ' ' . $timeFormat;

        if ($format === 'date') {
            $returnFormat = $dateFormat;
        } elseif ($format === 'time') {
            $returnFormat = $timeFormat;
        } elseif ($format === 'date-time') {
            $returnFormat = $dateFormat . ' ' . $timeFormat;
        }
        return $returnFormat;
    }
}