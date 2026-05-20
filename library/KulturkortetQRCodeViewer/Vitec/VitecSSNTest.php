<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\Vitec;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class VitecSSNTest extends TestCase
{
    #[TestDox('Formats a compact SSN correctly')]
    public function testFormatCompactSSN(): void
    {
        $ssn = '199001011234';
        $formatted = VitecSSN::formatSSN($ssn);
        $this->assertEquals('19900101-1234', $formatted);
    }

    #[TestDox('Returns non-compact SSN unchanged')]
    public function testFormatNonCompactSSN(): void
    {
        $ssn = '19900101-1234';
        $formatted = VitecSSN::formatSSN($ssn);
        $this->assertEquals($ssn, $formatted);
    }

    #[TestDox('Returns invalid SSN unchanged')]
    public function testFormatInvalidSSN(): void
    {
        $ssn = 'invalid_ssn';
        $formatted = VitecSSN::formatSSN($ssn);
        $this->assertEquals($ssn, $formatted);
    }
}
