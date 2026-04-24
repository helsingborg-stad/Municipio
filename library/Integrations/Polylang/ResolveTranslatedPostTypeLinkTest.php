<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveTranslatedPostTypeLinkTest extends TestCase
{
    #[TestDox('addHooks() defers registration to init/20')]
    public function testAddHooksRegistersInitAction(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $sut       = new ResolveTranslatedPostTypeLink($wpService);

        $sut->addHooks();

        static::assertSame(
            [['init', [$sut, 'registerPostTypeHooks'], 20]],
            $wpService->methodCalls['addAction']
        );
    }

    #[TestDox('registerPostTypeHooks() registers an option filter for each public post type')]
    public function testRegisterPostTypeHooksRegistersOptionFilters(): void
    {
        $wpService = new FakeWpService([
            'addFilter'    => true,
            'addRewriteRule' => null,
            'getOption'    => false,
            'getPostTypes' => ['books', 'events'],
        ]);

        $sut = new ResolveTranslatedPostTypeLink(
            $wpService,
            translatedPostResolver: static fn (int $id): int => $id
        );

        $sut->registerPostTypeHooks();

        static::assertSame(
            ['option_page_for_books', 'option_page_for_events'],
            array_column($wpService->methodCalls['addFilter'], 0)
        );
    }

    #[TestDox('registerPostTypeHooks() does nothing when Polylang is not active')]
    public function testRegisterPostTypeHooksEarlyReturnsWhenPolylangInactive(): void
    {
        $wpService = new FakeWpService([
            'addFilter'      => true,
            'addRewriteRule' => null,
            'getOption'      => 42,
            'getPostTypes'   => ['books'],
        ]);

        // No translatedPostResolver and Polylang functions are undefined in tests.
        $sut = new ResolveTranslatedPostTypeLink($wpService);

        $sut->registerPostTypeHooks();

        static::assertArrayNotHasKey('addFilter', $wpService->methodCalls);
        static::assertArrayNotHasKey('addRewriteRule', $wpService->methodCalls);
        static::assertArrayNotHasKey('getPostTypes', $wpService->methodCalls);
    }

    #[TestDox('registerPostTypeHooks() adds rewrite rules for all language variants')]
    public function testRegisterPostTypeHooksAddsRewriteRulesForAllLanguages(): void
    {
        $wpService = new FakeWpService([
            'addFilter'      => true,
            'addRewriteRule' => null,
            'getOption'      => 42,
            'getPageUri'     => static fn (int $id): string => match ($id) {
                42      => 'butik',
                100     => 'shop',
                default => '',
            },
            'getPostTypes'   => ['product'],
        ]);

        $sut = new ResolveTranslatedPostTypeLink(
            $wpService,
            translatedPostResolver: static fn (int $id): int => $id,
            postTranslationsResolver: static fn (int $id): array => ['sv' => 42, 'en' => 100]
        );

        $sut->registerPostTypeHooks();

        $regexes = array_column($wpService->methodCalls['addRewriteRule'], 0);

        static::assertContains('butik/?$', $regexes);
        static::assertContains('butik/page/?([0-9]{1,})/?$', $regexes);
        static::assertContains('shop/?$', $regexes);
        static::assertContains('shop/page/?([0-9]{1,})/?$', $regexes);
    }

    #[TestDox('resolveTranslatedPageId() returns the translated page ID when Polylang resolves one')]
    public function testResolveTranslatedPageIdReturnsTranslatedPageId(): void
    {
        $sut = $this->getSut(static fn (int $id): int => $id === 42 ? 100 : 0);

        static::assertSame(100, $sut->resolveTranslatedPageId(42));
    }

    #[TestDox('resolveTranslatedPageId() returns the original page ID when no translation exists')]
    public function testResolveTranslatedPageIdReturnsOriginalWhenNoTranslation(): void
    {
        $sut = $this->getSut(static fn (int $id): int => 0);

        static::assertSame(42, $sut->resolveTranslatedPageId(42));
    }

    #[TestDox('resolveTranslatedPageId() returns the original value when the page ID is not numeric')]
    public function testResolveTranslatedPageIdReturnsOriginalWhenNotNumeric(): void
    {
        $sut = $this->getSut(static fn (int $id): int => 0);

        static::assertSame('not-an-id', $sut->resolveTranslatedPageId('not-an-id'));
    }

    private function getSut(?Closure $translatedPostResolver = null): ResolveTranslatedPostTypeLink
    {
        return new ResolveTranslatedPostTypeLink(new FakeWpService(), $translatedPostResolver);
    }
}
