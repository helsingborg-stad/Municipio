<?php

namespace Municipio\StyleguideCss\CssVariables;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class NullCssVariablesCollectionTest extends TestCase
{
    #[TestDox('returns an empty array of variables')]
    public function testReturnsEmptyArray(): void
    {
        $collection = new NullCssVariablesCollection();
        $this->assertEmpty($collection->getVariables());
    }

    #[TestDox('returns an empty string when cast to string')]
    public function testToStringReturnsEmpty(): void
    {
        $collection = new NullCssVariablesCollection();
        $this->assertSame('', (string) $collection);
    }
}
