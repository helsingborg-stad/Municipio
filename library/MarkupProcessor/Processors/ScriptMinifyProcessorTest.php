<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ScriptMinifyProcessorTest extends TestCase
{
    #[TestDox('minifies JavaScript content within <script> tags in the markup')]
    public function testProcess(): void
    {
        $processor = new ScriptMinifyProcessor();

        $input = '<script>/* Comment */ console.log("Hello");</script>';
        $expectedOutput = '<script> console.log("Hello");</script>';

        $this->assertEquals($expectedOutput, $processor->process($input));
    }
}
