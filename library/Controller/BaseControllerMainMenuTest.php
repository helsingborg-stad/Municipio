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
    }

    private function hasMainMenu(array $primaryMenu, array $headerData): bool
    {
        $controller = (new \ReflectionClass(BaseController::class))->newInstanceWithoutConstructor();
        $controller->data['primaryMenu'] = $primaryMenu;
        $controller->data['headerData'] = $headerData;

        return (new \ReflectionMethod(BaseController::class, 'hasMainMenu'))->invoke($controller);
    }
}
