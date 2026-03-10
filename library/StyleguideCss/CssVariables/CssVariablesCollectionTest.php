<?php

namespace Municipio\StyleguideCss\CssVariables;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class CssVariablesCollectionTest extends TestCase
{
    #[TestDox('returns an array of provided CSS variables')]
    public function testGetVariables(): void
    {
        $cssVariable1 = new CssVariable('--primary-color', '#ff0000');
        $cssVariable2 = new CssVariable('--secondary-color', '#00ff00');

        $collection = new CssVariablesCollection([$cssVariable1, $cssVariable2]);
        $variables = $collection->getVariables();

        $this->assertCount(2, $variables);
        $this->assertSame($cssVariable1, $variables[0]);
        $this->assertSame($cssVariable2, $variables[1]);
    }

    #[TestDox('throws if non CssVariableInterface objects are provided')]
    public function testInvalidVariables(): void
    {
        $this->expectException(\TypeError::class);
        new CssVariablesCollection([new \stdClass()]);
    }
}
