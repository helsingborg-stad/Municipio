<?php

declare(strict_types=1);

namespace Municipio\CommonFieldGroups;

use AcfService\Implementations\FakeAcfService;
use Municipio\CommonFieldGroups\SubFieldValueResolver\SubFieldValueResolverInterface;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests registration of filters that retrieve common field values.
 */
class FilterGetFieldToRetriveCommonValuesTest extends TestCase
{
    #[TestDox('It initializes common field overrides after ACF fields are imported on a subsite.')]
    public function testAddHooksRegistersInitActionAfterAcfFieldImportOnSubsite(): void
    {
        $wpService = new FakeWpService([
            'isMainSite' => false,
            'addAction'  => true,
        ]);
        $instance  = $this->createInstance($wpService);

        $instance->addHooks();

        static::assertSame('init', $wpService->methodCalls['addAction'][0][0]);
        static::assertSame([$instance, 'initializeFieldsToFilter'], $wpService->methodCalls['addAction'][0][1]);
        static::assertSame(15, $wpService->methodCalls['addAction'][0][2]);
    }

    #[TestDox('It does not initialize common field overrides on the main site.')]
    public function testAddHooksDoesNotRegisterActionOnMainSite(): void
    {
        $wpService = new FakeWpService([
            'isMainSite' => true,
        ]);
        $instance  = $this->createInstance($wpService);

        $instance->addHooks();

        static::assertArrayNotHasKey('addAction', $wpService->methodCalls);
    }

    /**
     * Create the subject with isolated dependencies.
     */
    private function createInstance(FakeWpService $wpService): FilterGetFieldToRetriveCommonValues
    {
        return new FilterGetFieldToRetriveCommonValues(
            $wpService,
            new FakeAcfService([]),
            $this->createStub(SiteSwitcherInterface::class),
            $this->createStub(CommonFieldGroupsConfigInterface::class),
            $this->createStub(SubFieldValueResolverInterface::class),
        );
    }
}