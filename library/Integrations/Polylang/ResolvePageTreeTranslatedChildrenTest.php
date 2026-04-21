<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolvePageTreeTranslatedChildrenTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolvePageTreeTranslatedChildren::class, $this->getSut());
    }

    #[TestDox('addHooks() registers the page tree translated children filter')]
    public function testAddHooksRegistersTranslatedChildrenFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'getPostType' => 'page',
            'getTheTitle' => 'Translated child',
        ]);

        $sut = new ResolvePageTreeTranslatedChildren($wpService);

        $sut->addHooks();

        static::assertSame(
            [['Municipio/Navigation/PageTree/Children', [$sut, 'resolveTranslatedChildren'], 10, 2]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('resolveTranslatedChildren() returns translated children when Polylang mappings are available')]
    public function testResolveTranslatedChildrenReturnsTranslatedChildren(): void
    {
        $sut = $this->getSut(
            postTranslationsResolver: static fn (int $postId): array => ['en' => 116, 'sv' => 128],
            currentLanguageResolver: static fn (): string => 'sv',
            translatedPostResolver: static fn (int $postId, string $lang): int => 124,
            defaultLanguageResolver: static fn (): string => 'en',
            childrenByParentResolver: static fn (int $postId, string $postType): array => [
                ['ID' => 120, 'post_title' => 'Testpage sub (en)', 'post_parent' => 116, 'post_type' => 'page']
            ],
            currentPostIdResolver: static fn (): int => 124,
            ancestorIdsResolver: static fn (): array => [0, 128, 124]
        );

        $translatedChildren = $sut->resolveTranslatedChildren([], 128);

        static::assertCount(1, $translatedChildren);
        static::assertSame(124, $translatedChildren[0]['ID']);
        static::assertSame(128, $translatedChildren[0]['post_parent']);
        static::assertTrue($translatedChildren[0]['active']);
        static::assertTrue($translatedChildren[0]['ancestor']);
    }

    #[TestDox('resolveTranslatedChildren() returns existing children when children are already provided')]
    public function testResolveTranslatedChildrenReturnsExistingChildrenWhenAlreadyProvided(): void
    {
        $sut = $this->getSut();

        $existingChildren = [['ID' => 1, 'post_title' => 'Existing', 'post_parent' => 10, 'post_type' => 'page']];

        static::assertSame($existingChildren, $sut->resolveTranslatedChildren($existingChildren, 128));
    }

    #[TestDox('resolveTranslatedChildren() returns empty children when no translation source exists')]
    public function testResolveTranslatedChildrenReturnsEmptyChildrenWhenNoSourceExists(): void
    {
        $sut = $this->getSut(
            postTranslationsResolver: static fn (int $postId): array => ['sv' => 128],
            currentLanguageResolver: static fn (): string => 'sv',
            translatedPostResolver: static fn (int $postId, string $lang): int => 0
        );

        static::assertSame([], $sut->resolveTranslatedChildren([], 128));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $postTranslationsResolver Optional post translations resolver.
     * @param ?Closure $currentLanguageResolver Optional current language resolver.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     * @param ?Closure $defaultLanguageResolver Optional default language resolver.
     * @param ?Closure $childrenByParentResolver Optional children resolver.
     * @param ?Closure $currentPostIdResolver Optional current post ID resolver.
     * @param ?Closure $ancestorIdsResolver Optional ancestor IDs resolver.
     *
     * @return ResolvePageTreeTranslatedChildren The system under test.
     */
    private function getSut(
        ?Closure $postTranslationsResolver = null,
        ?Closure $currentLanguageResolver = null,
        ?Closure $translatedPostResolver = null,
        ?Closure $defaultLanguageResolver = null,
        ?Closure $childrenByParentResolver = null,
        ?Closure $currentPostIdResolver = null,
        ?Closure $ancestorIdsResolver = null
    ): ResolvePageTreeTranslatedChildren {
        return new ResolvePageTreeTranslatedChildren(
            new FakeWpService([
                'addFilter' => true,
                'getPostType' => 'page',
                'getTheTitle' => 'Translated child',
            ]),
            $postTranslationsResolver,
            $currentLanguageResolver,
            $translatedPostResolver,
            $defaultLanguageResolver,
            $childrenByParentResolver,
            $currentPostIdResolver,
            $ancestorIdsResolver
        );
    }
}
