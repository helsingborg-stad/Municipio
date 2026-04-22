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
                ['id' => 2001, 'page_id' => 9, 'post_type' => 'page', 'label' => 'Home'],
                ['id' => 2002, 'page_id' => 106, 'post_type' => 'page', 'label' => 'Home - Svenska'],
                ['id' => 2003, 'page_id' => 114, 'post_type' => 'page', 'label' => 'Testpage 1 (en)'],
                ['id' => 2004, 'page_id' => 128, 'post_type' => 'page', 'label' => 'Testpage 2 (sv)'],
                ['id' => 2005, 'page_id' => 124, 'post_type' => 'page', 'label' => 'Testpage sub (sv)', 'post_parent' => 2004],
            ],
            'mobile'
        );

        static::assertSame([106, 128, 124], array_column($filtered, 'page_id'));
    }

    #[TestDox('filterItemsByCurrentLanguage() falls back to id for page tree items')]
    public function testFilterItemsByCurrentLanguageFallsBackToIdForPageTreeItems(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv',
            postLanguageResolver: static fn (int $postId): string => $postId === 124 ? 'sv' : 'en'
        );

        $filtered = $sut->filterItemsByCurrentLanguage(
            [
                ['id' => 114, 'post_type' => 'page', 'label' => 'Testpage 1 (en)'],
                ['id' => 124, 'post_type' => 'page', 'label' => 'Testpage sub (sv)'],
            ],
            'mobile'
        );

        static::assertSame([124], array_column($filtered, 'id'));
    }

    #[TestDox('filterItemsByCurrentLanguage() filters all menu identifiers')]
    public function testFilterItemsByCurrentLanguageFiltersAllIdentifiers(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv',
            postLanguageResolver: static fn (int $postId): string => 'en'
        );

        $menuItems = [
            ['id' => 9, 'post_type' => 'page', 'label' => 'Home'],
            ['id' => 106, 'post_type' => 'page', 'label' => 'Home - Svenska'],
        ];

        static::assertSame([], $sut->filterItemsByCurrentLanguage($menuItems, 'primary'));
    }

    #[TestDox('filterItemsByCurrentLanguage() falls back to the request lang when current language is unavailable')]
    public function testFilterItemsByCurrentLanguageFallsBackToRequestLang(): void
    {
        $previousLang = $_GET['lang'] ?? null;
        $_GET['lang'] = 'sv';

        try {
            $sut = $this->getSut(
                currentLanguageResolver: null,
                postLanguageResolver: static fn (int $postId): string => $postId === 124 ? 'sv' : 'en'
            );

            $filtered = $sut->filterItemsByCurrentLanguage(
                [
                    ['id' => 114, 'post_type' => 'page', 'label' => 'Testpage 1 (en)'],
                    ['id' => 124, 'post_type' => 'page', 'label' => 'Testpage sub (sv)'],
                ],
                'mobile'
            );

            static::assertSame([124], array_column($filtered, 'id'));
        } finally {
            if ($previousLang === null) {
                unset($_GET['lang']);
            } else {
                $_GET['lang'] = $previousLang;
            }
        }
    }

    #[TestDox('filterItemsByCurrentLanguage() filters nested children recursively')]
    public function testFilterItemsByCurrentLanguageFiltersNestedChildrenRecursively(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv',
            postLanguageResolver: static fn (int $postId): string => in_array($postId, [26, 1024, 847], true) ? 'sv' : 'en'
        );

        $filtered = $sut->filterItemsByCurrentLanguage(
            [
                [
                    'id' => 26,
                    'post_type' => 'page',
                    'children' => [
                        ['id' => 1024, 'post_type' => 'page', 'label' => 'Konferens & moten'],
                        ['id' => 11169, 'post_type' => 'page', 'label' => 'Conference & meetings'],
                        ['id' => 847, 'post_type' => 'page', 'label' => 'Boende'],
                        ['id' => 11184, 'post_type' => 'page', 'label' => 'Accommodation'],
                    ],
                ],
            ],
            'mobile'
        );

        static::assertSame([1024, 847], array_column($filtered[0]['children'], 'id'));
    }

    #[TestDox('filterItemsByCurrentLanguage() filters custom post types with Polylang language mappings')]
    public function testFilterItemsByCurrentLanguageFiltersCustomPostTypes(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv',
            postLanguageResolver: static fn (int $postId): string => in_array($postId, [1024, 847], true) ? 'sv' : 'en'
        );

        $filtered = $sut->filterItemsByCurrentLanguage(
            [
                ['id' => 1024, 'post_type' => 'besoka-uppleva', 'label' => 'Konferens & moten'],
                ['id' => 11169, 'post_type' => 'besoka-uppleva', 'label' => 'Conference & meetings'],
                ['id' => 847, 'post_type' => 'besoka-uppleva', 'label' => 'Boende'],
                ['id' => 11184, 'post_type' => 'besoka-uppleva', 'label' => 'Accommodation'],
            ],
            'mobile'
        );

        static::assertSame([1024, 847], array_column($filtered, 'id'));
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
