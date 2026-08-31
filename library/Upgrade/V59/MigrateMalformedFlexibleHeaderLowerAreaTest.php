<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V59;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateMalformedFlexibleHeaderLowerAreaTest extends TestCase
{
    public function testMigrateClearsKnownMalformedLowerAreaAndHiddenStorage(): void
    {
        $items = [
            'header-search-form', 'search-modal', 'collapsible-search', 'logotype', 'brand-text', 'user', 'userGroupUrl',
            'primary', 'language', 'tab', 'drawer', 'mega-menu', 'siteselector',
        ];
        $themeMods = [
            'header_apperance' => 'flexible',
            'header_sortable_section_main_lower' => json_encode($items),
            'header_sortable_hidden_storage' => json_encode([
                'header_sortable_section_main_lower' => array_fill_keys($items, ['align' => 'right', 'margin' => 'none']),
            ]),
        ];
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateMalformedFlexibleHeaderLowerArea($wpService))->migrate();

        static::assertContains(['header_sortable_section_main_lower', []], $wpService->methodCalls['setThemeMod'] ?? []);
        $storage = $wpService->methodCalls['setThemeMod'][1][1] ?? null;
        $storage = is_string($storage) ? json_decode($storage, true) : $storage;
        static::assertSame([], $storage['header_sortable_section_main_lower'] ?? null);
    }

    public function testMigrateDoesNotChangeConfiguredLowerArea(): void
    {
        $themeMods = [
            'header_apperance' => 'flexible',
            'header_sortable_section_main_lower' => ['primary'],
            'header_sortable_hidden_storage' => '{}',
        ];
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateMalformedFlexibleHeaderLowerArea($wpService))->migrate();

        static::assertEmpty($wpService->methodCalls['setThemeMod'] ?? []);
    }
}