<?php

declare(strict_types=1);

namespace Municipio\Controller;

use Municipio\Controller\Header\Header;
use PHPUnit\Framework\TestCase;

final class BaseControllerMainMenuTest extends TestCase
{
    public function testSkipLinkRequiresRenderedPrimaryNavigation(): void
    {
        static::assertFalse($this->hasMainMenu(['items' => ['item']], [
            'upper' => $this->createHeaderWithComponents(['mega-menu']),
            'lower' => $this->createHeaderWithComponents([]),
        ]));

        static::assertTrue($this->hasMainMenu(['items' => ['item']], [
            'upper' => $this->createHeaderWithComponents(['primary']),
            'lower' => $this->createHeaderWithComponents([]),
        ]));

        static::assertTrue($this->hasMainMenu(['items' => ['item']], [
            'upper' => $this->createHeaderWithComponents([]),
            'lower' => $this->createHeaderWithComponents(['primary']),
        ]));

        static::assertFalse($this->hasMainMenu(['items' => []], [
            'upper' => $this->createHeaderWithComponents(['primary']),
        ]));

        static::assertTrue($this->hasMainMenu(
            ['items' => []],
            ['upper' => $this->createHeaderWithComponents(['drawer'])],
            ['items' => ['item']],
        ));

        static::assertFalse($this->hasMainMenu(
            ['items' => []],
            ['upper' => $this->createHeaderWithComponents(['drawer'])],
            ['items' => []],
        ));

        static::assertFalse($this->hasMainMenu(
            ['items' => []],
            ['upper' => $this->createHeaderWithComponents(['primary'])],
            ['items' => ['item']],
        ));
    }

    private function hasMainMenu(array $primaryMenu, array $headerData, array $mobileMenu = ['items' => []]): bool
    {
        $controller = (new \ReflectionClass(BaseController::class))->newInstanceWithoutConstructor();
        $controller->data['primaryMenu'] = $primaryMenu;
        $controller->data['headerData'] = $headerData;
        $controller->data['mobileMenu'] = $mobileMenu;

        return (new \ReflectionMethod(BaseController::class, 'hasMainMenu'))->invoke($controller);
    }

    private function createHeaderWithComponents(array $components): Header
    {
        $header = $this->createMock(Header::class);
        $header->method('getMenuItems')->willReturn(array_fill_keys($components, new \stdClass()));

        return $header;
    }
}
