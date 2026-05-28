<?php

declare(strict_types=1);

namespace Municipio\Admin\Gutenberg\Blocks;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;

if (!function_exists(__NAMESPACE__ . '\get_field')) {
    /**
     * Test double for the ACF get_field helper.
     *
     * @param string $fieldKey The field key.
     *
     * @return mixed
     */
    function get_field(string $fieldKey): mixed
    {
        return BlockManagerTest::getFieldValue($fieldKey);
    }
}

if (!function_exists(__NAMESPACE__ . '\get_field_object')) {
    /**
     * Test double for the ACF get_field_object helper.
     *
     * @param string $fieldKey The field key or field name.
     *
     * @return array<string, mixed>|false
     */
    function get_field_object(string $fieldKey): array|false
    {
        return BlockManagerTest::getFieldObject($fieldKey);
    }
}

/**
 * @internal
 */
class BlockManagerTest extends TestCase
{
    /**
     * @var array<string, mixed>
     */
    private static array $fieldValues = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private static array $fieldObjects = [];

    protected function setUp(): void
    {
        parent::setUp();

        self::$fieldValues = [];
        self::$fieldObjects = [];
    }

    /**
     * Get a stubbed field value.
     *
     * @param string $fieldKey The field key.
     *
     * @return mixed
     */
    public static function getFieldValue(string $fieldKey): mixed
    {
        return self::$fieldValues[$fieldKey] ?? null;
    }

    /**
     * Get a stubbed field object.
     *
     * @param string $fieldKey The field key or field name.
     *
     * @return array<string, mixed>|false
     */
    public static function getFieldObject(string $fieldKey): array|false
    {
        return self::$fieldObjects[$fieldKey] ?? false;
    }

    public function testBuildDataHandlesArrayValuedFields(): void
    {
        self::$fieldObjects = [
            'background_gradient' => [
                'name' => 'background_gradient',
            ],
        ];

        $gradient = [
            ['color' => '#000000', 'stop' => 0],
            ['color' => '#ffffff', 'stop' => 100],
        ];

        $blockManager = new BlockManager();

        $result = $blockManager->buildData([
            'background_gradient' => $gradient,
        ]);

        static::assertSame($gradient, $result['background_gradient']);
    }

    public function testValidateFieldsIgnoresArrayValuedBlockData(): void
    {
        self::$fieldObjects = [
            'field_background_gradient' => [
                'required' => false,
                'parent'   => 'group_container',
                'value'    => [
                    ['color' => '#000000', 'stop' => 0],
                ],
            ],
        ];

        $blockManager = new BlockManager();
        $method = new ReflectionMethod(BlockManager::class, 'validateFields');
        $method->setAccessible(true);

        $result = $method->invoke($blockManager, [
            'background_gradient'  => [
                ['color' => '#000000', 'stop' => 0],
            ],
            '_background_gradient' => 'field_background_gradient',
        ]);

        static::assertTrue($result);
    }
}
