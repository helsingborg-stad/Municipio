<?php

namespace Municipio\CommonFieldGroups;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\CommonFieldGroups\CommonFieldGroupsConfigInterface;

class DisableFieldsThatAreCommonlyManagedOnSubsitesTest extends TestCase
{
    private DisableFieldsThatAreCommonlyManagedOnSubsites $instance;
    private FakeWpService $wpService;
    private FakeAcfService $acfService;
    private SiteSwitcher $siteSwitcher;
    private CommonFieldGroupsConfigInterface $config;

    protected function setUp(): void
    {
        $this->wpService = new FakeWpService([
            'addAction'     => function ($hook, $callback) {
                $this->assertEquals('admin_init', $hook);
                $this->assertIsCallable($callback);
                return true;
            },
            'addFilter'     => function ($hook, $callback, $priority, $acceptedArgs) {
                $this->assertEquals('acf/prepare_field', $hook);
                $this->assertIsCallable($callback);
                $this->assertEquals(10, $priority);
                $this->assertEquals(1, $acceptedArgs);
                return true;
            },
            'getAdminUrl'   => fn($siteId, $slug) => "https://example.com/site-{$siteId}/{$slug}",
            'getMainSiteId' => fn() => 1,
            '__'            => fn($text) => $text,
            '__e'           => fn($text) => $text,
        ]);

        $this->acfService   = new FakeAcfService([]);
        $this->siteSwitcher = $this->createMock(SiteSwitcher::class);

        $this->config = new class implements CommonFieldGroupsConfigInterface {
            public function isEnabled(): bool
            {
                return true;
            }

            public function getShouldDisableFieldGroups(): bool
            {
                return true;
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
                    ['group_1'],
                    ['group_2']
                ];
            }
        };

        $this->instance = new DisableFieldsThatAreCommonlyManagedOnSubsites(
            $this->wpService,
            $this->acfService,
            $this->siteSwitcher,
            $this->config
        );
    }

    #[TestDox('It should register the disableFieldGroups action on addHooks.')]
    public function testAddHooks(): void
    {
        $this->instance->addHooks();
    }

    #[TestDox('It should disable field groups that are commonly managed on subsites.')]
    public function testProcessFieldAddsNoticeForGroup(): void
    {
        $field  = ['parent' => 'group_1', 'id' => 'field_1'];
        $result = $this->instance->processField($field, 'group_1');

        $this->assertArrayHasKey('_name', $result);
        $this->assertEquals('acf_disabled_field', $result['_name']);
    }

    #[TestDox('It should return the field for an unmatched group.')]
    public function testProcessFieldReturnsFieldForUnmatchedGroup(): void
    {
        $field  = ['parent' => 'group_2', 'id' => 'field_1'];
        $result = $this->instance->processField($field, 'group_1');

        $this->assertEquals($field, $result);
    }
}
