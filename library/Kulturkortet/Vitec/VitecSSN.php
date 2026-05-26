<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Vitec;

class VitecSSN
{
    /**
     * Formats a given SSN to the compact format used by Vitec (YYYYMMDD-XXXX).
     * If the input is already in the correct format, it will be returned as is.
     * If the input does not match the expected formats, it will be returned unchanged.
     * @param string $ssn The input SSN to format.
     * @return string The formatted SSN in compact format or the original input if it does not match expected formats.
     */
    public static function formatSSN(string $ssn): string
    {
        // compact SSN YYYYMMDDXXXX
        if (preg_match('/^\d{12}$/', $ssn)) {
            return substr($ssn, 0, 8) . '-' . substr($ssn, 8, 4);
        }
        return $ssn;
    }
}
