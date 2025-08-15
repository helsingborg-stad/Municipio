<?php

namespace Municipio\CommonFieldGroups;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\MockObject\MockObject;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;

class FilterGetFieldToRetriveCommonValuesTest extends TestCase
{
    private FilterGetFieldToRetriveCommonValues $instance;
    private FakeWpService $wpService;
    private FakeAcfService $acfService;
    private CommonFieldGroupsConfigInterface $config;
    private SiteSwitcherInterface|MockObject $siteSwitcher;

    protected function setUp(): void
    {
        $this->wpService = new FakeWpService([
            'getMainSiteId' => fn() => 1,
            'isMainSite' => fn() => false, // Simulate we're on a subsite
            'addAction' => fn($hook, $callback) => true,
            'addFilter' => fn($hook, $callback, $priority = 10, $args = 1) => true,
            'getOption' => fn($optionName) => $this->getOptionMock($optionName),
        ]);

        $this->acfService = new FakeAcfService([
            'acfGetFields' => fn($groupId) => $this->getFieldsMock($groupId),
        ]);

        $this->config = new class implements CommonFieldGroupsConfigInterface {
            public function isEnabled(): bool
            {
                return true;
            }

            public function getShouldDisableFieldGroups(): bool
            {
                return false;
            }

            public function getOptionsKey(): string
            {
                return 'sitewide_common_acf_fieldgroups';
            }

            public function getOptionsSelectFieldKey(): string
            {
                return 'sitewide_common_acf_fieldgroup_value';
            }

            public function getAcfFieldGroupsToFilter(): array
            {
                return [
                    ['group_test_select']
                ];
            }
        };

        $this->siteSwitcher = $this->createMock(SiteSwitcherInterface::class);

        $this->instance = new FilterGetFieldToRetriveCommonValues(
            $this->wpService, 
            $this->acfService, 
            $this->siteSwitcher, 
            $this->config
        );
    }

    private function getOptionMock(string $optionName): mixed
    {
        // Simulate raw WordPress option values
        $options = [
            'options_test_select_field' => 'option_value',  // Raw stored value (key)
            '_options_test_select_field' => 'field_123',    // ACF field key metadata
        ];

        return $options[$optionName] ?? null;
    }

    private function getFieldsMock(string $groupId): array
    {
        if ($groupId === 'group_test_select') {
            return [
                [
                    'key' => 'field_123',
                    'name' => 'test_select_field',
                    'type' => 'select',
                    'return_format' => 'label',
                    'choices' => [
                        'option_value' => 'Human Readable Label'
                    ]
                ]
            ];
        }
        if ($groupId === 'group_test_value') {
            return [
                [
                    'key' => 'field_456',
                    'name' => 'test_value_field',
                    'type' => 'select',
                    'return_format' => 'value',
                    'choices' => [
                        'option_value' => 'Human Readable Label'
                    ]
                ]
            ];
        }
        return [];
    }

    /**
     * @testdox Should respect return_format setting for select fields with label return format
     */
    public function testRespectsReturnFormatForSelectFieldWithLabel(): void
    {
        // Mock ACF service to return formatted value when getField is called
        $this->acfService = new FakeAcfService([
            'acfGetFields' => fn($groupId) => $this->getFieldsMock($groupId),
            'getField' => fn($fieldName, $postId) => $fieldName === 'test_select_field' && $postId === 'option' 
                ? 'Human Readable Label'  // Formatted value for return_format=label
                : null,
        ]);

        $this->instance = new FilterGetFieldToRetriveCommonValues(
            $this->wpService, 
            $this->acfService, 
            $this->siteSwitcher, 
            $this->config
        );

        // Mock the site switcher to execute the callback
        $this->siteSwitcher->expects($this->once())
            ->method('runInSite')
            ->with($this->equalTo(1), $this->isType('callable'))
            ->willReturnCallback(function ($siteId, $callable) {
                return $callable();
            });

        // Initialize the fields to filter
        $this->instance->initializeFieldsToFilter();

        // Verify that the formatted value is stored, not the raw value
        $reflection = new \ReflectionClass($this->instance);
        $fieldsKeyValueStore = $reflection->getProperty('fieldsKeyValueStore');
        $fieldsKeyValueStore->setAccessible(true);
        $store = $fieldsKeyValueStore->getValue($this->instance);

        $this->assertEquals('Human Readable Label', $store['options_test_select_field']);
    }

    /**
     * @testdox Should keep raw values for non-select fields or value return format
     */
    public function testKeepsRawValuesForValueReturnFormat(): void
    {
        // Mock a field with return_format = 'value' (should keep raw value)
        $this->acfService = new FakeAcfService([
            'acfGetFields' => fn($groupId) => [
                [
                    'key' => 'field_456',
                    'name' => 'test_value_field',
                    'type' => 'select',
                    'return_format' => 'value', // Should return raw value
                    'choices' => [
                        'option_value' => 'Human Readable Label'
                    ]
                ]
            ],
            'getField' => fn($fieldName, $postId) => $fieldName === 'test_value_field' && $postId === 'option'
                ? 'option_value'  // Raw value for return_format=value
                : null,
        ]);

        // Update config to filter the value field
        $this->config = new class implements CommonFieldGroupsConfigInterface {
            public function isEnabled(): bool { return true; }
            public function getShouldDisableFieldGroups(): bool { return false; }
            public function getOptionsKey(): string { return 'sitewide_common_acf_fieldgroups'; }
            public function getOptionsSelectFieldKey(): string { return 'sitewide_common_acf_fieldgroup_value'; }
            public function getAcfFieldGroupsToFilter(): array { return [['group_test_value']]; }
        };

        $this->instance = new FilterGetFieldToRetriveCommonValues(
            $this->wpService, 
            $this->acfService, 
            $this->siteSwitcher, 
            $this->config
        );

        // For return_format=value, we should keep the raw value
        $this->siteSwitcher->expects($this->once())
            ->method('runInSite')
            ->willReturnCallback(function ($siteId, $callable) {
                return $callable();
            });

        $this->instance->initializeFieldsToFilter();

        $reflection = new \ReflectionClass($this->instance);
        $fieldsKeyValueStore = $reflection->getProperty('fieldsKeyValueStore');
        $fieldsKeyValueStore->setAccessible(true);
        $store = $fieldsKeyValueStore->getValue($this->instance);

        $this->assertEquals('option_value', $store['options_test_value_field']);
    }

    /**
     * @testdox Should handle repeater fields correctly without affecting return format logic
     */
    public function testHandlesRepeaterFieldsCorrectly(): void
    {
        // Mock a repeater field
        $this->acfService = new FakeAcfService([
            'acfGetFields' => fn($groupId) => [
                [
                    'key' => 'field_repeater',
                    'name' => 'test_repeater_field',
                    'type' => 'repeater',
                    'sub_fields' => [
                        [
                            'key' => 'field_sub1',
                            'name' => 'sub_field_1',
                            'type' => 'text'
                        ]
                    ]
                ]
            ],
        ]);

        // Update config for repeater test
        $this->config = new class implements CommonFieldGroupsConfigInterface {
            public function isEnabled(): bool { return true; }
            public function getShouldDisableFieldGroups(): bool { return false; }
            public function getOptionsKey(): string { return 'sitewide_common_acf_fieldgroups'; }
            public function getOptionsSelectFieldKey(): string { return 'sitewide_common_acf_fieldgroup_value'; }
            public function getAcfFieldGroupsToFilter(): array { return [['group_test_repeater']]; }
        };

        // Mock getOption to return repeater count and subfield values
        $this->wpService = new FakeWpService([
            'getMainSiteId' => fn() => 1,
            'isMainSite' => fn() => false,
            'addAction' => fn($hook, $callback) => true,
            'addFilter' => fn($hook, $callback, $priority = 10, $args = 1) => true,
            'getOption' => fn($optionName) => match($optionName) {
                'options_test_repeater_field' => 2, // 2 repeater entries
                '_options_test_repeater_field' => 'field_repeater',
                'options_test_repeater_field_0_sub_field_1' => 'value1',
                'options_test_repeater_field_1_sub_field_1' => 'value2',
                default => null
            },
        ]);

        $this->instance = new FilterGetFieldToRetriveCommonValues(
            $this->wpService, 
            $this->acfService, 
            $this->siteSwitcher, 
            $this->config
        );

        $this->siteSwitcher->expects($this->once())
            ->method('runInSite')
            ->willReturnCallback(function ($siteId, $callable) {
                return $callable();
            });

        $this->instance->initializeFieldsToFilter();

        $reflection = new \ReflectionClass($this->instance);
        $fieldsKeyValueStore = $reflection->getProperty('fieldsKeyValueStore');
        $fieldsKeyValueStore->setAccessible(true);
        $store = $fieldsKeyValueStore->getValue($this->instance);

        // Verify repeater structure is maintained
        $this->assertIsArray($store['options_test_repeater_field']);
        $this->assertCount(2, $store['options_test_repeater_field']);
        $this->assertEquals('value1', $store['options_test_repeater_field'][0]['sub_field_1']);
        $this->assertEquals('value2', $store['options_test_repeater_field'][1]['sub_field_1']);
    }
}