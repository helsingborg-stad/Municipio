<?php

namespace Municipio\Helper;

/*
 * Returns format for date and time
 */
class DateFormat
{
    /**
     * Get the date format based on the specified format.
     *
     * @param string $format The format to retrieve the date format for.
     * @return string The date format.
     */
    public static function getDateFormat($format)
    {
        $defaultTime = 'H:i';
        $defaultDate = 'Y-m-d';
        $dateFormat  = function_exists('get_option') && !empty(get_option('date_format')) ? get_option('date_format') : $defaultDate;
        $timeFormat  = function_exists('get_option') && !empty(get_option('time_format')) ? get_option('time_format') : $defaultTime;

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
    /**
     * Strip the seconds from a time string.
     *
     * @param string $timeString The time string to strip seconds from.
     * @return string The time string without seconds.
     */
    public static function stripSeconds($timeString)
    {
        // Create a DateTime object from the time string
        $dateTime = \DateTime::createFromFormat('H:i:s', $timeString);

        // If the DateTime object was successfully created, it means the string had seconds
        if ($dateTime) {
            return $dateTime->format('H:i');
        } else {
            // If the creation failed, it means the string was already in the 'H:i' format or invalid
            $dateTime = \DateTime::createFromFormat('H:i', $timeString);
            if ($dateTime) {
                // If valid, return the original string
                return $timeString;
            }
        }
        return '';
    }
}
