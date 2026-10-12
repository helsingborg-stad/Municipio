<?php

declare(strict_types=1);

namespace Municipio\Controller;

use PHPUnit\Framework\TestCase;

final class BaseControllerMainMenuTest extends TestCase
{
    public function testSkipLinkRequiresRenderedPrimaryNavigation(): void
    {
        static::assertFalse($this->hasMainMenu(['items' => ['item']], [
            'upperItems' => ['right' => ['mega-menu' => []]],
            'lowerItems' => [],
        ]));

        static::assertTrue($this->hasMainMenu(['items' => ['item']], [
            'upperItems' => ['left' => ['primary' => []]],
            'lowerItems' => [],
        ]));

        static::assertTrue($this->hasMainMenu(['items' => ['item']], [
            'upperItems' => [],
            'lowerItems' => ['center' => ['primary' => []]],
        ]));

        static::assertFalse($this->hasMainMenu(['items' => []], [
            'upperItems' => ['left' => ['primary' => []]],
        ]));

        static::assertTrue($this->hasMainMenu(
            ['items' => []],
            ['upperItems' => ['right' => ['drawer' => []]]],
            ['items' => ['item']],
        ));

        static::assertFalse($this->hasMainMenu(
            ['items' => []],
            ['upperItems' => ['right' => ['drawer' => []]]],
            ['items' => []],
        ));

        static::assertFalse($this->hasMainMenu(
            ['items' => []],
            ['upperItems' => ['right' => ['primary' => []]]],
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
}
