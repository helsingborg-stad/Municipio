<?php

declare(strict_types=1);

namespace Modularity\Module\Menu\Decorator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ListingTest extends TestCase
{
    #[DataProvider('provideWrapperClasses')]
    public function testDecorateBuildsSmarterLargeBreakpointColumnClasses(int $amountOfItems, string $expectedLargeBreakpointClass): void
    {
        $listing = new Listing([], null);

        $result = $listing->decorate([
            'menu' => [
                'items' => array_fill(0, $amountOfItems, ['id' => 1]),
            ],
        ]);

        $this->assertSame([
            'o-layout-grid--cols-1',
            'o-layout-grid--cols-2@md',
            $expectedLargeBreakpointClass,
        ], $result['wrapperClasses']);
    }

    public static function provideWrapperClasses(): array
    {
        return [
            'single item' => [1, 'o-layout-grid--cols-1@lg'],
            'two items' => [2, 'o-layout-grid--cols-2@lg'],
            'three items' => [3, 'o-layout-grid--cols-3@lg'],
            'four items' => [4, 'o-layout-grid--cols-4@lg'],
            'six items prefers three columns' => [6, 'o-layout-grid--cols-3@lg'],
            'eight items keeps four columns' => [8, 'o-layout-grid--cols-4@lg'],
        ];
    }
}