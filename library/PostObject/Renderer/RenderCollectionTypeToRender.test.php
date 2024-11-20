<?php

namespace Municipio\PostObject\Renderer;

use PHPUnit\Framework\TestCase;

class RenderCollectionTypeToRenderTest extends TestCase
{
    private RenderTypeToRenderInterface $renderTypeToRender;

    protected function setUp(): void
    {
        $renderFactory            = $this->createMock(RenderDirectorInterface::class);
        $this->renderTypeToRender = new RenderCollectionTypeToRender($renderFactory, []);
    }

    /**
     * @testdox returns RenderInterface for RenderCollectionType
     * @dataProvider renderTypesProvider
     */
    public function testGetRenderTypeFromRenderReturnsRenderBuilderInterfaceForBlockItem(RenderCollectionType $rendertype): void
    {
        $this->assertInstanceOf(RenderInterface::class, $this->renderTypeToRender->getRenderFromRenderType($rendertype));
    }

    public function renderTypesProvider(): array
    {
        $cases = [];
        foreach (RenderCollectionType::cases() as $case) {
            $cases[$case->value] = [$case];
        }

        return $cases;
    }
}
