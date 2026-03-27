<?php

namespace Municipio\StyleguideCss\CssVariables;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class CssVariablesRendererTest extends TestCase
{
    #[TestDox('renders CSS variables in :root')]
    public function testRender(): void
    {
        $cssVariables = [
            new CssVariable('--primary-color', '#ff0000'),
            new CssVariable('--secondary-color', '#00ff00'),
        ];

        $renderer = new CssVariablesRenderer();
        $css = $renderer->render(...$cssVariables);

        // Remove whitespace differences for robust comparison
        $expectedBlock = ":root {\n    --primary-color: #ff0000;\n    --secondary-color: #00ff00;\n}";
        $normalizedCss = preg_replace('/\s+/', '', $css);
        $normalizedExpected = preg_replace('/\s+/', '', $expectedBlock);

        $this->assertStringContainsString($normalizedExpected, $normalizedCss);
    }
}
