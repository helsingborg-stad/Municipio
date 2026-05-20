<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\Vitec;

class VitecSSN
{
    public static function formatSSN(string $ssn): string
    {
        // compact SSN YYYYMMDDXXXX
        if (preg_match('/^\d{12}$/', $ssn)) {
            // Format is already correct
            return substr($ssn, 0, 8) . '-' . substr($ssn, 8, 4);
        }
        return $ssn;
    }
}
