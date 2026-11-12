<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V66;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

class Version66Test extends TestCase
{
    #[TestDox('adds the matching header contrast token for legacy colour selections')]
    public function testAddsPairedHeaderContrastToken(): void
    {
        $wpService = $this->createWpService([
            'component' => [
                'scope:s-header-flexible-upper' => [
                    'header' => ['--c-header--color--surface' => 'var(--color--secondary)'],
                ],
                'scope:s-header-flexible-lower' => [
                    'header' => ['--c-header--color--surface' => 'var(--color--palette-4)'],
                ],
            ],
        ]);

        (new Version66($wpService))->upgradeToVersion();

        static::assertCount(1, $wpService->writes);
        $tokens = json_decode($wpService->writes[0], true, flags: JSON_THROW_ON_ERROR);
        static::assertSame('var(--color--secondary-contrast)', $tokens['component']['scope:s-header-flexible-upper']['header']['--c-header--color--surface-contrast']);
        static::assertSame('var(--color--palette-4-contrast)', $tokens['component']['scope:s-header-flexible-lower']['header']['--c-header--color--surface-contrast']);
    }

    #[TestDox('keeps an explicitly configured header contrast token')]
    public function testKeepsExplicitHeaderContrastToken(): void
    {
        $wpService = $this->createWpService([
            'component' => [
                '__general__' => [
                    'header' => [
                        '--c-header--color--surface' => 'var(--color--primary)',
                        '--c-header--color--surface-contrast' => 'var(--color--secondary-contrast)',
                    ],
                ],
            ],
        ]);

        (new Version66($wpService))->upgradeToVersion();

        static::assertSame([], $wpService->writes);
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
