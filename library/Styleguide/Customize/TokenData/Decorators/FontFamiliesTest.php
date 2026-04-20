<?php

namespace Municipio\Styleguide\Customize\TokenData\Decorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;

class FontFamiliesTest extends TestCase
{
    #[TestDox('turns --font-family-base into a select with options')]
    public function testDecorate(): void
    {
        $tokenData = [
            'categories' => [[
                'id' => 'typography',
                'settings' => [[
                    'variable' => '--font-family-base',
                    'type' => 'font',
                    'default' => 'Default Font',
                ]],
            ]],
        ];

        $decorator = new FontFamilies(static::createWpService());
        $decoratedData = $decorator->decorate($tokenData);

        static::assertSame('select', $decoratedData['categories'][0]['settings'][0]['type']);
        static::assertContains(
            [
                'value' => 'Arial, sans-serif',
                'label' => 'Arial',
            ],
            $decoratedData['categories'][0]['settings'][0]['options'],
        );
    }

    #[TestDox('adds default font value to options')]
    public function testDecorateWithDefaultFont(): void
    {
        $tokenData = [
            'categories' => [[
                'id' => 'typography',
                'settings' => [[
                    'variable' => '--font-family-base',
                    'type' => 'font',
                    'default' => '"Roboto", sans-serif',
                ]],
            ]],
        ];

        $decorator = new FontFamilies(static::createWpService());
        $decoratedData = $decorator->decorate($tokenData);

        static::assertContains(
            [
                'value' => '"Roboto", sans-serif',
                'label' => '"Roboto", sans-serif',
            ],
            $decoratedData['categories'][0]['settings'][0]['options'],
        );
    }

    private static function createWpService(): ApplyFilters
    {
        return new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };
    }
}
