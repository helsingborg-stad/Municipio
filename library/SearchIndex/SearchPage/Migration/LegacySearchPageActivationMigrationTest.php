<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\SearchPage\Migration;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class LegacySearchPageActivationMigrationTest extends TestCase
{
    public function testEnablesSearchPageWhenLegacyAddonWasActive(): void
    {
        $wpService = new FakeWpService(['getOption' => true]);
        $acfService = new FakeAcfService(['updateField' => true]);

        (new LegacySearchPageActivationMigration($wpService, $acfService))->migrate();

        static::assertSame('search_index_search_page_enabled', $acfService->methodCalls['updateField'][0][0]);
        static::assertTrue($acfService->methodCalls['updateField'][0][1]);
        static::assertSame('option', $acfService->methodCalls['updateField'][0][2]);
    }

    public function testDoesNothingWithoutLegacyActivationMarker(): void
    {
        $wpService = new FakeWpService(['getOption' => false]);
        $acfService = new FakeAcfService(['updateField' => true]);

        (new LegacySearchPageActivationMigration($wpService, $acfService))->migrate();

        static::assertArrayNotHasKey('updateField', $acfService->methodCalls);
    }
}