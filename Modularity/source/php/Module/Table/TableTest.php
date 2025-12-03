<?php

declare(strict_types=1);

namespace Modularity\Module\Table;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    #[TestDox('unicodeConvert() handles null input')]
    public function testUnicodeConvertHandlesNullInput(): void
    {
        $this->assertEquals('', Table::unicodeConvert(null));
    }
}
