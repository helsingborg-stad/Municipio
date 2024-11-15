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
        $factory = new PostObjectRendererFactory();
        $this->assertInstanceOf(PostObjectRendererInterface::class, $factory->create($case));
    }

    public function caseProvider()
    {
        return array_map(fn ($case) => [$case], PostObjectRendererType::cases());
    }
}
