<?php

namespace Municipio\PostObject\Renderer\PostObjectCollectionRenderer;

use PHPUnit\Framework\TestCase;

class PostObjectCollectionRendererFactoryTest extends TestCase
{
    /**
     * @testdox The factory returns the correct instance.
     * @dataProvider caseProvider
     */
    public function testGetInstance($case)
    {
        $factory = new PostObjectCollectionRendererFactory();
        $this->assertInstanceOf(PostObjectCollectionRendererInterface::class, $factory->create($case));
    }

    public function caseProvider()
    {
        return array_map(fn ($case) => [$case], PostObjectCollectionRendererType::cases());
    }
}
