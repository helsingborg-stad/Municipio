<?php

declare(strict_types=1);

namespace Municipio\Theme\Footer;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class FooterAreasTest extends TestCase
{
    #[TestDox('getAreaIds returns all registered footer areas regardless of column count')]
    public function testGetAreaIdsReturnsAllRegisteredFooterAreas(): void
    {
        $footerAreas = new FooterAreas($this->createWpServiceWithColumnCount('1'));

        static::assertSame(
            [
                'footer-area',
                'footer-area-column-1',
                'footer-area-column-2',
                'footer-area-column-3',
                'footer-area-column-4',
                'footer-area-column-5',
            ],
            $footerAreas->getAreaIds(),
        );
    }

    #[TestDox('getColumnCount returns the stored design token value')]
    public function testGetColumnCountReturnsStoredDesignTokenValue(): void
    {
        $footerAreas = new FooterAreas($this->createWpServiceWithColumnCount('4'));

        static::assertSame(4, $footerAreas->getColumnCount());
    }

    #[TestDox('getColumnCount falls back to the styleguide default when no token is stored')]
    public function testGetColumnCountFallsBackToDefaultWhenNoTokenIsStored(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $default,
        ]);

        static::assertSame(FooterAreas::DEFAULT_COLUMN_COUNT, (new FooterAreas($wpService))->getColumnCount());
    }

    #[TestDox('getColumnCount falls back to the styleguide default when tokens lack a column count')]
    public function testGetColumnCountFallsBackToDefaultWhenTokensLackColumnCount(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed =>
                $name === 'tokens' ? '{"component":{"__general__":{"footer":{}}}}' : $default,
        ]);

        static::assertSame(FooterAreas::DEFAULT_COLUMN_COUNT, (new FooterAreas($wpService))->getColumnCount());
    }

    #[TestDox('getColumnCount clamps values to the available footer areas')]
    public function testGetColumnCountClampsValuesToAvailableFooterAreas(): void
    {
        $footerAreas = new FooterAreas($this->createWpServiceWithColumnCount('99'));

        static::assertSame(FooterAreas::AREA_COUNT, $footerAreas->getColumnCount());
        static::assertSame(1, (new FooterAreas($this->createWpServiceWithColumnCount('0')))->getColumnCount());
    }

    private function createWpServiceWithColumnCount(string $columnCount): FakeWpService
    {
        $tokens = json_encode([
            'component' => [
                '__general__' => [
                    'footer' => ['--c-footer--columns-count' => $columnCount],
                ],
            ],
        ]);

        return new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed =>
                $name === 'tokens' ? $tokens : $default,
        ]);
    }
}
