<?php

namespace Municipio\CommonFieldGroups;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\MockObject\MockObject;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;

class FilterGetFieldToRetriveCommonValuesTest extends TestCase
{
    private $instance;
    private FakeWpService $wpService;
    private FakeAcfService $acfService;
    private CommonFieldGroupsConfigInterface $config;
    private SiteSwitcherInterface|MockObject $siteSwitcher;

    protected function setUp(): void
    {
        $this->wpService = new FakeWpService([
            'getMainSiteId' => 1,
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

        $this->siteSwitcher = $this->createMock(SiteSwitcherInterface::class);

        $this->instance = new FilterGetFieldToRetriveCommonValues($this->wpService, $this->acfService, $this->siteSwitcher, $this->config);
    }



    /**
     * @testdox filterFieldValue should return value from main blog if option key exists in fields to filter and blog id is 1
     */
    public function testfilterFieldValue(): void
    {
        $this->instance->fieldsToFilter = ['field_1', 'field_2'];

        $this->siteSwitcher->expects($this->once())
            ->method('getFieldFromSite')
            ->with($this->equalTo(1), $this->equalTo('field_name'))
            ->willReturn('main_blog_value');

        $this->assertEquals(
            'main_blog_value', 
            $this->instance->filterFieldValue('local_value', 'option', ['key' => 'field_1', 'name' => 'field_name'])
        );

        
    }

    //Test that filterFieldValue should return local value if option key does not exists in fields to filter
    public function testfilterFieldValue2(): void
    {
        $this->instance->fieldsToFilter = ['field_1', 'field_2'];

        $this->siteSwitcher->expects($this->never())
            ->method('getFieldFromSite');

        $this->assertEquals(
            'local_value', 
            $this->instance->filterFieldValue('local_value', 'option', ['key' => 'field_3', 'name' => 'field_name'])
        );
    }

    //Test that only id option pass 
    public function testfilterFieldValue3(): void
    {
        $this->instance->fieldsToFilter = ['field_1', 'field_2'];

        $this->siteSwitcher->expects($this->never())
            ->method('getFieldFromSite');

        $this->assertEquals(
            'local_value', 
            $this->instance->filterFieldValue('local_value', 15, ['key' => 'field_1', 'name' => 'field_name'])
        );
    }

}
