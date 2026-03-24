<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class EmptyIdRemoveProcessorTest extends TestCase
{
    #[TestDox('removes empty id attributes from the markup')]
    public function testProcess(): void
    {
        $processor = new EmptyIdRemoveProcessor();

        $input = '<div id="">Content</div><span id="not-empty">More content</span>';
        $expectedOutput = '<div>Content</div><span id="not-empty">More content</span>';

        $this->assertEquals($expectedOutput, $processor->process($input));
    }
}
