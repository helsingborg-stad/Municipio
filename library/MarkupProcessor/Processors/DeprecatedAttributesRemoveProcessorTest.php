<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DeprecatedAttributesRemoveProcessorTest extends TestCase
{
    #[TestDox('removes deprecated attributes from the markup')]
    public function testProcess(): void
    {
        $processor = new DeprecatedAttributesRemoveProcessor();

        $input = '<style type="text/css">body { color: red; }</style><script type="text/javascript">console.log("Hello");</script><div style="text/css">Content</div>';
        $expectedOutput = '<style >body { color: red; }</style><script >console.log("Hello");</script><div style="text/css">Content</div>';

        $this->assertEquals($expectedOutput, $processor->process($input));
    }
}
