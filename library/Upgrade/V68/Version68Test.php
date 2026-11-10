<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V68;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

class Version68Test extends TestCase
{
    #[TestDox('removes legacy literal footer colour tokens')]
    public function testRemovesLegacyLiteralFooterColorTokens(): void
    {
        $wpService = $this->createWpService([
            'component' => [
                '__general__' => [
                    'footer' => [
                        '--c-footer--color' => '#000000',
                        '--c-footer--background-color' => '#fefefe',
                        '--c-footer--subfooter-color' => '#000000',
                        '--c-footer--color--surface' => 'var(--color--primary)',
                    ],
                ],
            ],
        ]);

        (new Version68($wpService))->upgradeToVersion();

        static::assertCount(1, $wpService->writes);
        $tokens = json_decode($wpService->writes[0], true, flags: JSON_THROW_ON_ERROR);
        $footer = $tokens['component']['__general__']['footer'];
        static::assertArrayNotHasKey('--c-footer--color', $footer);
        static::assertArrayNotHasKey('--c-footer--background-color', $footer);
        static::assertArrayNotHasKey('--c-footer--subfooter-color', $footer);
        static::assertSame('var(--color--primary)', $footer['--c-footer--color--surface']);
    }

    /**
     * @param array<string, mixed> $tokens
     */
    private function createWpService(array $tokens): GetThemeMod&SetThemeMod
    {
        return new class ($tokens) implements GetThemeMod, SetThemeMod {
            /** @var list<string> */
            public array $writes = [];

            /** @param array<string, mixed> $tokens */
            public function __construct(private array $tokens) {}

            public function getThemeMod(string $name, mixed $default = false): mixed
            {
                return $name === 'tokens' ? json_encode($this->tokens, JSON_THROW_ON_ERROR) : $default;
            }

            public function setThemeMod(string $name, mixed $value): bool
            {
                $this->writes[] = (string) $value;
                return true;
            }
        };
    }
}
