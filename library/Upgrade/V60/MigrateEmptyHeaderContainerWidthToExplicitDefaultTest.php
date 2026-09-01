<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V60;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateEmptyHeaderContainerWidthToExplicitDefaultTest extends TestCase
{
    public function testMigratesEmptyGeneralAndScopedHeaderWidths(): void
    {
        $themeMods = [
            'tokens' => json_encode([
                'token' => [],
                'component' => [
                    '__general__' => [
                        'header' => ['--c-header--container-max-width' => ''],
                    ],
                    'scope:s-header-flexible-upper' => [
                        'header' => ['--c-header--container-max-width' => ''],
                    ],
                ],
            ]),
        ];
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateEmptyHeaderContainerWidthToExplicitDefault($wpService))->migrate();

        $tokens = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);

        static::assertSame('var(--container-width)', $tokens['component']['__general__']['header']['--c-header--container-max-width'] ?? null);
        static::assertSame('var(--container-width)', $tokens['component']['scope:s-header-flexible-upper']['header']['--c-header--container-max-width'] ?? null);
    }

    public function testDoesNotCreateOrReplaceExistingHeaderWidths(): void
    {
        $themeMods = [
            'tokens' => json_encode([
                'token' => [],
                'component' => [
                    '__general__' => [
                        'header' => ['--c-header--container-max-width' => '100%'],
                    ],
                    'scope:s-header-flexible-lower' => [
                        'header' => [],
                    ],
                ],
            ]),
        ];
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateEmptyHeaderContainerWidthToExplicitDefault($wpService))->migrate();

        static::assertEmpty($wpService->methodCalls['setThemeMod'] ?? []);
    }
}
