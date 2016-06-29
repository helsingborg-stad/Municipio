<?php

namespace Intranet\Helper;

class DataCleaner
{
    /**
     * Parses a phone number string with libphonenumber
     * @link https://github.com/giggsey/libphonenumber-for-php
     *
     * @param  string $number Phone number to format
     * @return array          Formatted phone number (international and national)
     */
    public static function phoneNumber($number, $numberFormat = \libphonenumber\PhoneNumberFormat::NATIONAL)
    {
        if (is_null($number)) {
            return $number;
        }

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $number = $phoneUtil->parse($number, 'SE');

        return $phoneUtil->format($number, $numberFormat);
    }
}
