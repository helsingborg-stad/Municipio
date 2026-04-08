<?php

namespace Municipio\StyleguideCss\CssVariables;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class CssVariableTest extends TestCase
{
    #[TestDox('returns the correct name and value of the CSS variable')]
    public function testGetNameAndValue(): void
    {
        $variable = new CssVariable('--primary-color', '#ff0000');
        $this->assertEquals('--primary-color', $variable->getName());
        $this->assertEquals('#ff0000', $variable->getValue());
    }
}
