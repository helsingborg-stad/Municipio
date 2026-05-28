<?php

namespace Municipio\Admin\Gutenberg\Blocks;

use PHPUnit\Framework\TestCase;

if (!function_exists(__NAMESPACE__ . '\block_manager_test_state')) {
    /**
     * Gets or updates shared test state for BlockManager tests.
     *
     * @param array<string, mixed>|null $state The state update.
     *
     * @return array<string, mixed>
     */
    function block_manager_test_state(?array $state = null): array
    {
        static $testState = [
            'fields' => [],
        ];

        if ($state !== null) {
            $testState = array_merge($testState, $state);
        }

        return $testState;
    }
}

if (!function_exists(__NAMESPACE__ . '\get_field')) {
    /**
     * Test double for get_field().
     *
     * @param string $selector The field selector.
     *
     * @return mixed
     */
    function get_field($selector)
    {
        return block_manager_test_state()['fields'][$selector]['value'] ?? null;
    }
}

if (!function_exists(__NAMESPACE__ . '\get_field_object')) {
    /**
     * Test double for get_field_object().
     *
     * @param string $selector The field selector.
     *
     * @return array<string, mixed>|false
     */
    function get_field_object($selector)
    {
        return block_manager_test_state()['fields'][$selector] ?? false;
    }
}

/**
 * @internal
 */
class BlockManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        block_manager_test_state([
            'fields' => [
                'field_title' => [
                    'name'     => 'title',
                    'required' => false,
                    'value'    => 'Resolved title',
                ],
                'items' => [
                    'name' => 'items',
                ],
                'field_required_text' => [
                    'name'     => 'required_text',
                    'required' => true,
                    'value'    => '',
                    'parent'   => 'group_123',
                ],
            ],
        ]);
    }

    public function testBuildDataHandlesNonStringValuesWithoutTypeErrors(): void
    {
        $manager = (new \ReflectionClass(BlockManager::class))->newInstanceWithoutConstructor();

        $result = $manager->buildData([
            'title' => 'field_title',
            'items' => [
                ['label' => 'First item'],
            ],
        ]);

        $this->assertSame([
            'title' => 'Resolved title',
            'items' => [
                ['label' => 'First item'],
            ],
        ], $result);
    }

    public function testValidateFieldsIgnoresNonStringValuesAndStillValidatesStringFieldKeys(): void
    {
        $manager          = (new \ReflectionClass(BlockManager::class))->newInstanceWithoutConstructor();
        $validationMethod = new \ReflectionMethod(BlockManager::class, 'validateFields');
        $validationMethod->setAccessible(true);

        $result = $validationMethod->invoke($manager, [
            'items'          => [
                ['label' => 'First item'],
            ],
            'required_text' => 'field_required_text',
        ]);

        $this->assertFalse($result);
    }
}
