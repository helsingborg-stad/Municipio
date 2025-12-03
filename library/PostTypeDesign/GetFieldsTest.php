<?php

namespace Municipio\PostTypeDesign;

use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\GetFields;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\UpdateOption;

class GetFieldsTest extends TestCase
{
    public function testGetFieldsReturnsEmptyArrayIfNoFields()
    {
        $getFieldsInstance = new GetFields([]);

        $result = $getFieldsInstance->getFields();

        $this->assertEquals([], $result);
    }

    public function testGetFieldsReturnsAllWantedFieldsIfNoFilters()
    {
        $getFieldsInstance = new GetFields([
            $this->getFaultyField(),
            $this->getColorField(),
            $this->getLogotypeField()
        ]);

        $result = $getFieldsInstance->getFields();

        $this->assertCount(2, $result);

        $this->assertEquals($this->getColorField(), $result[0]);
        $this->assertEquals($this->getLogotypeField(), $result[1]);
    }

    public function testGetFieldsReturnsFilteredArray()
    {
        $getFieldsInstance = new GetFields([
            $this->getFaultyField(),
            $this->getColorField(),
            $this->getLogotypeField()
        ]);

        $result = $getFieldsInstance->getFields(['logotypes']);

        $this->assertCount(1, $result);
        $this->assertEquals($this->getLogotypeField(), $result[1]);
    }

    public function testGetFieldsKeysReturnsEmptyArrayIfNoFields()
    {
        $getFieldsInstance = new GetFields([]);

        $result = $getFieldsInstance->getFieldKeys(['logotypes']);

        $this->assertEquals([], $result);
    }


    public function testGetFieldsKeysReturnsAllWantedFieldsSettingsIdsIfNoFilters()
    {
        $getFieldsInstance = new GetFields([
            $this->getFaultyField(),
            $this->getColorField(),
            $this->getLogotypeField()
        ]);

        $result = $getFieldsInstance->getFieldKeys();

        $this->assertEquals($this->getColorField()['settings'], $result[0]);
        $this->assertEquals($this->getLogotypeField()['settings'], $result[1]);
    }

    public function testGetFieldsKeysReturnsFilteredFieldSettingsIds()
    {
        $getFieldsInstance = new GetFields([
            $this->getFaultyField(),
            $this->getColorField(),
            $this->getLogotypeField()
        ]);

        $result = $getFieldsInstance->getFieldKeys(['logotypes']);

        $this->assertEquals($this->getLogotypeField()['settings'], $result[1]);
    }

    private function getColorField()
    {
        return [
            'type'     => 'color',
            'settings' => 'field1',
            'choices'  => [
                'base' => 'Base'
            ],
            'output'   => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-base'
                ]
            ]
        ];
    }

    private function getLogotypeField()
    {
        return [
            'type'     => 'notValidFieldType',
            'settings' => 'field2',
            'section'  => 'municipio_customizer_section_logo',
            'choices'  => [
                'base' => 'Base'
            ],
        ];
    }

    private function getFaultyField()
    {
        return [
            'type'     => 'faultySetting',
            'settings' => 'field3',
            'choices'  => [
                'base' => 'Base'
            ],
        ];
    }

    private function getWpService(array $db = []): AddAction
    {
        return new class ($db) implements AddAction, GetOption, GetThemeMod, GetPostTypes, UpdateOption {
            public array $calls = ['addFilter' => [], 'getOption' => [], 'updateOption' => []];

            public function __construct(private array $db)
            {
            }

            public function addAction(string $tag, callable $functionToAdd, int $priority = 10, int $acceptedArgs = 1): bool
            {
                $this->calls['addAction'][] = func_get_args();
                return true;
            }

            public function getOption(string $option, mixed $defaultValue = false): mixed
            {
                $this->calls['getOption'][] = "ran";
                return $this->db['getOption'] ?? null;
            }

            public function getThemeMod(string $name, mixed $default = false): mixed
            {
                if (!empty($this->db['getThemeMod']) && is_array($this->db['getThemeMod'])) {
                    $themeMod = array_shift($this->db['getThemeMod']);
                } else {
                    $themeMod = $default;
                }

                return $themeMod;
            }

            public function getPostTypes(
                array|string $args = array(),
                string $output = 'names',
                string $operator = 'and'
            ): array {
                return $this->db['postTypes'] ?? [];
            }

            public function updateOption(string $option, mixed $value, string|bool $autoload = null): bool
            {
                $this->calls['updateOption'][] = "ran";
                return true;
            }
        };
    }
}
