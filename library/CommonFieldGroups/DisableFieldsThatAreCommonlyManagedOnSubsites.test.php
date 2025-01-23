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
                $this->assertEquals('init', $hook);
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

            public function getShouldFilterFieldValues(): bool
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

    /**
     * @testdox It should register the disableFieldGroups action on addHooks.
     */
    public function testAddHooks(): void
    {
        $this->instance->addHooks();
    }

    /**
     * @testdox It should disable field groups that are commonly managed on subsites.
     */
    public function testProcessFieldAddsNoticeForGroup(): void
    {
        $field  = ['parent' => 'group_1', 'id' => 'field_1'];
        $result = $this->instance->processField($field, 'group_1');

        $this->assertArrayHasKey('_name', $result);
        $this->assertEquals('acf_disabled_field', $result['_name']);
    }

    /**
     * @testdox It should return the field for an unmatched group.
     */
    public function testProcessFieldReturnsFieldForUnmatchedGroup(): void
    {
        $field  = ['parent' => 'group_2', 'id' => 'field_1'];
        $result = $this->instance->processField($field, 'group_1');

        $this->assertEquals($field, $result);
    }

    /**
     * @testdox It should return a base site url when generating the notices
     */
    public function testProcessFieldReturnsNoticeWithCorrectUrl(): void
    {
        $field  = ['parent' => 'group_1', 'id' => 'field_1'];
        $result = $this->instance->processField($field, 'group_1');

        $this->assertStringContainsString('https://example.com/site-1', $result['message']);
    }

    /**
     * @testdox It should return a base site url including query parameters when generating the notices
     */
    public function testProcessFieldReturnsNoticeWithCorrectUrlAndQueryParameters(): void
    {
        $_SERVER['PHP_SELF'] = '/wp-admin/post.php';
        $_GET                = ['utm_source' => 'acf_field_notice', 'action' => 'edit'];

        $field  = ['parent' => 'group_1', 'id' => 'field_1'];
        $result = $this->instance->processField($field, 'group_1');

        $this->assertStringContainsString('https://example.com/site-1', $result['message']);
        $this->assertStringContainsString('utm_source=acf_field_notice', $result['message']);
    }
}
