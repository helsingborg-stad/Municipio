<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V67;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

class Version67Test extends TestCase
{
    #[TestDox('removes legacy literal header colour tokens')]
    public function testRemovesLegacyLiteralHeaderColorTokens(): void
    {
        $wpService = $this->createWpService([
            'component' => [
                '__general__' => [
                    'header' => [
                        '--c-header--color' => '#000000',
                        '--c-header--background-color' => '#fefefe',
                        '--c-header--border-color' => '#000000',
                        '--c-header--color--surface' => 'var(--color--primary)',
                    ],
                ],
            ],
        ]);

        (new Version67($wpService))->upgradeToVersion();

        static::assertCount(1, $wpService->writes);
        $tokens = json_decode($wpService->writes[0], true, flags: JSON_THROW_ON_ERROR);
        $header = $tokens['component']['__general__']['header'];
        static::assertArrayNotHasKey('--c-header--color', $header);
        static::assertArrayNotHasKey('--c-header--background-color', $header);
        static::assertArrayNotHasKey('--c-header--border-color', $header);
        static::assertSame('var(--color--primary)', $header['--c-header--color--surface']);
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
