<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V51;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateCasualHeaderMarginTest extends TestCase
{
    #[TestDox('migrate sets remove-spacing for casual layout when header margin is missing')]
    public function testMigrateSetsRemoveSpacingForCasualLayoutWhenHeaderMarginIsMissing(): void
    {
        $themeMods = [
            'header_sortable_section_main_lower' => [],
            'header_sortable_section_main_upper' => ['logotype', 'primary'],
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateCasualHeaderMargin($wpService))->migrate();

        static::assertContains(
            ['header_margin', 'remove-spacing'],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );
    }

    #[TestDox('migrate does not set header margin for business-style layout')]
    public function testMigrateDoesNotSetHeaderMarginForBusinessStyleLayout(): void
    {
        $themeMods = [
            'header_sortable_section_main_lower' => ['primary'],
            'header_sortable_section_main_upper' => ['logotype', 'language', 'drawer', 'user'],
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateCasualHeaderMargin($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
    }
}
