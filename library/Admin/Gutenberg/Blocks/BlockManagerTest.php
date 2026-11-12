<?php

declare(strict_types=1);

namespace Municipio\Admin\Gutenberg\Blocks;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

function get_field_object(string $selector): array|false
{
    return BlockManagerTest::$fields[$selector] ?? false;
}

function get_field(string $selector): mixed
{
    return BlockManagerTest::$fields[$selector]['value'] ?? null;
}

class BlockManagerTest extends TestCase
{
    public static array $fields = [];

    protected function tearDown(): void
    {
        self::$fields = [];
    }

    public function testResolvesNestedEditorRepeaterValuesThroughAcf(): void
    {
        $rows = [['color' => 'primary', 'stop' => 0], ['color' => 'secondary', 'stop' => 100]];
        self::$fields['field_gradient'] = ['name' => 'background_gradient', 'value' => $rows];
        $manager = (new ReflectionClass(BlockManager::class))->newInstanceWithoutConstructor();

        $result = $manager->buildData([
            'field_gradient' => [
                'new-row-1' => ['field_color' => 'primary', 'field_stop' => 0],
                'new-row-2' => ['field_color' => 'secondary', 'field_stop' => 100],
            ],
        ]);

        static::assertSame(['background_gradient' => $rows], $result);
    }

    public function testResolvesSavedRepeaterMetadata(): void
    {
        $rows = [['color' => 'primary'], ['color' => 'secondary']];
        self::$fields['field_gradient'] = ['name' => 'background_gradient', 'value' => $rows];
        self::$fields['background_gradient'] = self::$fields['field_gradient'];
        $manager = (new ReflectionClass(BlockManager::class))->newInstanceWithoutConstructor();

        static::assertSame(
            ['background_gradient' => $rows],
            $manager->buildData([
                'background_gradient' => 2,
                '_background_gradient' => 'field_gradient',
            ]),
        );
    }

    public function testSingleStopProducesAValidGradient(): void
    {
        $manager = (new ReflectionClass(BlockManager::class))->newInstanceWithoutConstructor();
        $styles = new ReflectionMethod(BlockManager::class, 'getBlockBackgroundGradientStyles');

        static::assertSame(
            'background: linear-gradient(0deg, var(--color--primary), var(--color--primary));',
            $styles->invoke($manager, [
                'background_gradient' => [['color' => 'primary']],
                'background_gradient_type' => 'basic',
                'background_gradient_angle' => 0,
            ]),
        );
    }

    public function testValidatesNestedEditorValuesWithoutTreatingArraysAsStrings(): void
    {
        $manager = (new ReflectionClass(BlockManager::class))->newInstanceWithoutConstructor();
        $validate = new ReflectionMethod(BlockManager::class, 'validateFields');
        self::$fields['field_gradient'] = [
            'name' => 'background_gradient',
            'required' => true,
            'value' => [['color' => 'primary']],
        ];

        static::assertTrue($validate->invoke($manager, ['field_gradient' => ['row-0' => ['field_color' => 'primary']]]));
        self::$fields['field_gradient']['value'] = [];
        static::assertFalse($validate->invoke($manager, ['field_gradient' => []]));
    }
}
