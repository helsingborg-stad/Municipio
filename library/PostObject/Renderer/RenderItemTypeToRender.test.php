<?php

namespace Municipio\PostObject\Renderer;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;

class RenderItemTypeToRenderTest extends TestCase
{
    private RenderTypeToRenderInterface $renderTypeToRender;

    protected function setUp(): void
    {
        $renderFactory            = $this->createMock(RenderDirectorInterface::class);
        $postObject               = $this->createMock(PostObjectInterface::class);
        $this->renderTypeToRender = new RenderItemTypeToRender($renderFactory, $postObject);
    }

    /**
     * @testdox returns RenderInterface for RenderItemType
     * @dataProvider renderTypesProvider
     */
    public function testGetRenderTypeFromRenderReturnsRenderBuilderInterfaceForBlockItem(RenderItemType $rendertype): void
    {
        $this->assertInstanceOf(RenderInterface::class, $this->renderTypeToRender->getRenderFromRenderType($rendertype));
    }

    public function renderTypesProvider(): array
    {
        $cases = [];
        foreach (RenderItemType::cases() as $case) {
            $cases[$case->value] = [$case];
        }

        return $cases;
    }
}
