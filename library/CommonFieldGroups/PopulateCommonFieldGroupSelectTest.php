<?php

namespace Municipio\CommonFieldGroups;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use AcfService\Implementations\FakeAcfService;

class PopulateCommonFieldGroupSelectTest extends TestCase
{
    private PopulateCommonFieldGroupSelect $instance;
    private FakeWpService $wpService;
    private FakeAcfService $acfService;
    private CommonFieldGroupsConfigInterface $config;

    protected function setUp(): void
    {
        $this->wpService = new FakeWpService([
            'addFilter' => function ($hook, $callback) {
                $this->assertEquals('acf/load_field/name=sitewide_common_acf_fieldgroup_value', $hook);
                $this->assertIsCallable($callback);
                return true;
            },
        ]);

        $this->acfService = new FakeAcfService([
            'getFieldGroups' => function () {
                return [
                    [
                        'key'      => 'group_1',
                        'title'    => 'Group 1',
                        'location' => [
                            [
                                [
                                    'param' => 'options_page',
                                    'value' => 'settings_page',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key'      => 'group_2',
                        'title'    => 'Group 2',
                        'location' => [
                            [
                                [
                                    'param' => 'post_type',
                                    'value' => 'post',
                                ],
                            ],
                        ],
                    ],
                ];
            },
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
                return [];
            }
        };

        $this->instance = new PopulateCommonFieldGroupSelect($this->wpService, $this->acfService, $this->config);
    }

    /**
     * @testdox It should register the populateFieldGroupSelect filter on addHooks.
     */
    public function testAddHooks(): void
    {
        $this->instance->addHooks();
    }

    /**
     * @testdox It should populate the field group select field with choices from option pages.
     */
    public function testPopulateFieldGroupSelect(): void
    {
        $field = ['choices' => []];

        $result = $this->instance->populateFieldGroupSelect($field);

        $this->assertArrayHasKey('group_1', $result['choices']);
        $this->assertEquals('Group 1', $result['choices']['group_1']);
        $this->assertArrayNotHasKey('group_2', $result['choices']);
    }

    /**
     * @testdox It should correctly filter field groups connected to options pages.
     */
    public function testFilterOptionPages(): void
    {
        $fieldGroupWithOptionPage = [
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'value' => 'settings_page',
                    ],
                ],
            ],
        ];

        $fieldGroupWithoutOptionPage = [
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'value' => 'post',
                    ],
                ],
            ],
        ];

        $this->assertTrue($this->invokePrivateMethod($this->instance, 'filterOptionPages', [$fieldGroupWithOptionPage]));
        $this->assertFalse($this->invokePrivateMethod($this->instance, 'filterOptionPages', [$fieldGroupWithoutOptionPage]));
    }

    /**
     * Invoke a private method for testing
     *
     * @param object $object
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    private function invokePrivateMethod(object $object, string $method, array $parameters = [])
    {
        $reflection = new \ReflectionClass($object);
        $method     = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
