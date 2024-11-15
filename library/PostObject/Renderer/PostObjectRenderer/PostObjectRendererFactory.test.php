<?php

namespace Municipio\PostObject\Renderer\PostObjectRenderer;

use PHPUnit\Framework\TestCase;

class PostObjectRendererFactoryTest extends TestCase
{
    /**
     * @testdox Test that the factory returns the correct instance.
     * @dataProvider caseProvider
     */
    public function testGetInstance($case)
    {
        $this->assertInstanceOf($case->value, PostObjectRendererFactory::from($case->value)->getInstance());
    }

    public function caseProvider()
    {
        return array_map(fn ($case) => [$case], PostObjectRendererFactory::cases());
    }
}
