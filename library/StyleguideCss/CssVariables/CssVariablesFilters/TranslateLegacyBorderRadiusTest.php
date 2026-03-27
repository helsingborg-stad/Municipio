<?php

namespace Municipio\StyleguideCss\CssVariables\CssVariablesFilters;

use Municipio\StyleguideCss\CssVariables\CssVariable;
use Municipio\StyleguideCss\CssVariables\CssVariableInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TranslateLegacyBorderRadiusTest extends TestCase
{
    #[TestDox('modifies --border-radius value by dividing by 8')]
    public function testApply(): void
    {
        $cssVariable = new CssVariable('--border-radius', '16');
        $filter = new TranslateLegacyBorderRadius();

        // Apply the filter
        $filteredCssVariable = $filter->apply($cssVariable);

        // Assert that the value has been modified to '2' (16 / 8)
        $this->assertEquals('--border-radius', $filteredCssVariable->getName());
        $this->assertEquals('2', $filteredCssVariable->getValue());
    }
}
