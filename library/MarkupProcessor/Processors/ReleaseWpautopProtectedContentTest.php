<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\Content\WpAutopContentGuard\WpAutopContentGuard;
use Municipio\MarkupProcessor\MarkupProcessorInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ReleaseWpautopProtectedContentTest extends TestCase {
    #[TestDox('unwraps content wrapped in <pre class="wpautop-protected"> to prevent wpautop from adding <p> tags around it')]
    public function testProcess(): void {
        $processor = new ReleaseWpautopProtectedContent(new WpAutopContentGuard());

        $input = '<pre class="wpautop-protected"><p>Protected content</p></pre>';
        $expectedOutput = '<p>Protected content</p>';

        $this->assertEquals($expectedOutput, $processor->process($input));
    }
}