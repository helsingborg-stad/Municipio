<?php

declare(strict_types=1);

namespace Modularity\Module\NegativeMargin;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NegativeMarginTest extends TestCase
{
    #[DataProvider('amountProvider')]
    public function testWrapperClassUsesAClampedAmount(mixed $amount, string $expectedClass): void
    {
        $module = (new \ReflectionClass(NegativeMargin::class))->newInstanceWithoutConstructor();
        $module->data = ['amount' => $amount];

        static::assertSame([$expectedClass], $module->wrapperClasses());
    }

    /**
     * @return array<string, array{mixed, string}>
     */
    public static function amountProvider(): array
    {
        return [
            'default scale value' => [4, 'modularity-negative-margin--4'],
            'numeric ACF value' => ['12', 'modularity-negative-margin--12'],
            'lower bound' => [-2, 'modularity-negative-margin--0'],
            'upper bound' => [30, 'modularity-negative-margin--24'],
        ];
    }
}
