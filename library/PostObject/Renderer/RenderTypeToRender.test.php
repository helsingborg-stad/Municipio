<?php

namespace Municipio\PostObject\Renderer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RenderItemTypeToRenderTest extends TestCase
{
    private RenderTypeToRenderInterface $renderTypeToRender;

    protected function setUp(): void
    {
        $this->renderTypeToRender = new RenderTypeToRender($this->getRenderFactory());
    }

    private function getRenderFactory(): RenderDirectorInterface|MockObject
    {
        return $this->createMock(RenderDirectorInterface::class);
    }

    /**
     * @testdox returns RenderInterface for RenderItemType
     * @dataProvider renderTypesProvider
     */
    public function testGetRenderTypeFromRenderReturnsRenderBuilderInterfaceForBlockItem(RenderType $rendertype): void
    {
        $this->assertInstanceOf(RenderInterface::class, $this->renderTypeToRender->getRenderFromRenderType($rendertype));
    }

    public function renderTypesProvider(): array
    {
        $cases = [];
        foreach (RenderType::cases() as $case) {
            $cases[$case->value] = [$case];
        }

        return $cases;
    }
}
