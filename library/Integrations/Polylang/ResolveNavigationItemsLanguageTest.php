<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveNavigationItemsLanguageTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolveNavigationItemsLanguage::class, $this->getSut());
    }

    #[TestDox('addHooks() registers Municipio navigation items filter')]
    public function testAddHooksRegistersNavigationItemsFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        $sut = new ResolveNavigationItemsLanguage($wpService);

        $sut->addHooks();

        static::assertSame(
            [['Municipio/Navigation/Items', [$sut, 'filterItemsByCurrentLanguage'], 10, 2]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('filterItemsByCurrentLanguage() filters out pages not in current language for mobile menu')]
    public function testFilterItemsByCurrentLanguageFiltersMobileItems(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv',
            postLanguageResolver: static fn (int $postId): string => in_array($postId, [106, 128, 124], true) ? 'sv' : 'en'
        );

        $filtered = $sut->filterItemsByCurrentLanguage(
            [
                ['id' => 9, 'post_type' => 'page', 'label' => 'Home'],
                ['id' => 106, 'post_type' => 'page', 'label' => 'Home - Svenska'],
                ['id' => 114, 'post_type' => 'page', 'label' => 'Testpage 1 (en)'],
                ['id' => 128, 'post_type' => 'page', 'label' => 'Testpage 2 (sv)'],
                ['id' => 124, 'post_type' => 'page', 'label' => 'Testpage sub (sv)'],
            ],
            'mobile'
        );

        static::assertSame([106, 128, 124], array_column($filtered, 'id'));
    }

    #[TestDox('filterItemsByCurrentLanguage() does not filter non-mobile identifiers')]
    public function testFilterItemsByCurrentLanguageDoesNotFilterNonMobileIdentifiers(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv',
            postLanguageResolver: static fn (int $postId): string => 'en'
        );

        $menuItems = [
            ['id' => 9, 'post_type' => 'page', 'label' => 'Home'],
            ['id' => 106, 'post_type' => 'page', 'label' => 'Home - Svenska'],
        ];

        static::assertSame($menuItems, $sut->filterItemsByCurrentLanguage($menuItems, 'primary'));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $currentLanguageResolver Optional current language resolver.
     * @param ?Closure $postLanguageResolver Optional post language resolver.
     *
     * @return ResolveNavigationItemsLanguage
     */
    private function getSut(
        ?Closure $currentLanguageResolver = null,
        ?Closure $postLanguageResolver = null
    ): ResolveNavigationItemsLanguage {
        return new ResolveNavigationItemsLanguage(
            new FakeWpService(['addFilter' => true]),
            $currentLanguageResolver,
            $postLanguageResolver
        );
    }
}
