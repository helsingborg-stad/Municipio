<?php

declare(strict_types=1);

namespace Municipio\CommonFieldGroups;

use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CommonFieldGroupsConfigTest extends TestCase
{
    public function testUsesTheCurrentNetworkMainSiteWhenTheGroupDoesNotUseNetworkOne(): void
    {
        $config = $this->createConfig();

        static::assertSame(12, $config->getSourceSiteId([
            'sitewide_common_acf_fieldgroups_use_network_one_base_site' => false,
        ]));
    }

    public function testUsesNetworkOneBaseSiteByDefault(): void
    {
        $config = $this->createConfig();

        static::assertSame(1, $config->getSourceSiteId([]));
    }

    private function createConfig(): CommonFieldGroupsConfig
    {
        $wpService = new FakeWpService([
            'getMainSiteId' => static fn(?int $networkId = null): int => $networkId === 1 ? 1 : 12,
        ]);
        $acfService = new FakeAcfService();
        $siteSwitcher = $this->createMock(SiteSwitcher::class);

        return new CommonFieldGroupsConfig($wpService, $acfService, $siteSwitcher);
    }
}
