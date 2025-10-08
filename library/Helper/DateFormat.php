<?php

namespace Municipio\Helper;

use DateTime;
use DateTimeZone;

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

        switch ($format) {
            case 'date':
                return $dateFormat;
            case 'time':
                return $timeFormat;
            case 'date-badge':
                return 'U';
            case 'date-time':
            default:
                return $dateFormat . ' ' . $timeFormat;
        }
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

    /**
     * Get the current locale of the site.
     *
     * @return string The current locale.
     */
    public static function getLocale()
    {
        return get_locale();
    }

    /**
     * Get the current timezone of the site.
     *
     * @return string The current timezone.
     */
    public static function getTimezone(): ?string
    {
        // Get the offset string, like +02:00
        $timezoneOffset = wp_timezone_string();

        // Check if the returned timezone is already in IANA format
        if (preg_match('/^[A-Za-z_]+\/[A-Za-z_]+$/', $timezoneOffset)) {
            return $timezoneOffset; // Already an IANA timezone
        }

        // Parse the offset, convert to seconds
        $offsetSeconds = (new DateTimeZone($timezoneOffset))->getOffset(new DateTime("now"));

        // Get the corresponding timezone name (IANA) from the offset
        $timezoneName = timezone_name_from_abbr('', $offsetSeconds, 1);

        // Return the IANA timezone or the original offset if no matching IANA timezone
        return $timezoneName ?: null;
    }
}
