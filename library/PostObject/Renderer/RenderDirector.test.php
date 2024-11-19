<?php

namespace Municipio\PostObject\Renderer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RenderDirectorTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            RenderDirector::class,
            new RenderDirector($this->getRenderBuilder())
        );
    }

    private function getRenderBuilder(): RenderBuilderInterface|MockObject
    {
        return $this->createMock(RenderBuilderInterface::class);
    }
}
