<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WpService\Implementations\FakeWpService;

class ResolveTranslatedBreadcrumbAncestorsTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(
            ResolveTranslatedBreadcrumbAncestors::class,
            $this->getSut(),
        );
    }

    #[TestDox('addHooks() registers the Municipio/Breadcrumbs/Items filter')]
    public function testAddHooksRegistersFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $sut = new ResolveTranslatedBreadcrumbAncestors($wpService);

        $sut->addHooks();

        static::assertSame(
            [['Municipio/Breadcrumbs/Items', [$sut, 'resolveTranslatedAncestors'], 10, 1]],
            $wpService->methodCalls['addFilter'],
        );
    }

    #[TestDox('resolveTranslatedAncestors() replaces a wrong-language ancestor with its translation')]
    public function testResolveTranslatedAncestorsReplacesWrongLanguageItem(): void
    {
        // Swedish parent page (ID=10) is the wrong-language ancestor in the English breadcrumb.
        // Its English translation is ID=20 ("Visit, Experience & Conference").
        $sut = $this->getSut(
            currentLanguageResolver: static fn(): string => 'en',
            postLanguageResolver: static fn(int $id): string => match ($id) {
                10 => 'sv',
                20 => 'en',
                default => '',
            },
            translatedPostResolver: static fn(int $id, string $lang): int => match ([$id, $lang]) {
                [10, 'en'] => 20,
                default => 0,
            },
            posts: [
                20 => ['ID' => 20, 'post_title' => 'Visit, Experience & Conference', 'post_parent' => 0],
            ],
            pageLinks: [20 => 'http://localhost:8080/hbgcom/en/visit-experience/'],
        );

        $input = [
            5 => ['label' => 'Home', 'href' => 'http://localhost:8080/hbgcom/', 'current' => false, 'icon' => 'home'],
            10 => ['label' => 'Besöka, uppleva & konferens', 'href' => 'http://localhost:8080/hbgcom/besoka-uppleva/', 'current' => false, 'icon' => 'chevron_right'],
        ];

        $result = $sut->resolveTranslatedAncestors($input);

        // Swedish item (key=10) must be replaced by its English translation (key=20).
        static::assertArrayNotHasKey(10, $result);
        static::assertArrayHasKey(20, $result);
        static::assertSame('Visit, Experience & Conference', $result[20]['label']);
        static::assertSame('http://localhost:8080/hbgcom/en/visit-experience/', $result[20]['href']);

        // Home item must be unchanged.
        static::assertArrayHasKey(5, $result);
        static::assertSame('Home', $result[5]['label']);
    }

    #[TestDox('resolveTranslatedAncestors() leaves correct-language items unchanged')]
    public function testResolveTranslatedAncestorsLeavesCorrectLanguageItemsUnchanged(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn(): string => 'en',
            postLanguageResolver: static fn(int $id): string => 'en',
            translatedPostResolver: static fn(int $id, string $lang): int => 0,
        );

        $input = [
            5 => ['label' => 'Home', 'href' => '/', 'current' => false, 'icon' => 'home'],
            20 => ['label' => 'Visit, Experience & Conference', 'href' => '/en/visit/', 'current' => false, 'icon' => 'chevron_right'],
        ];

        static::assertSame($input, $sut->resolveTranslatedAncestors($input));
    }

    #[TestDox('resolveTranslatedAncestors() returns items unchanged when Polylang is not active')]
    public function testResolveTranslatedAncestorsReturnUnchangedWhenPolylangInactive(): void
    {
        $sut = $this->getSut();

        $input = [
            10 => ['label' => 'Besöka', 'href' => '/besoka/', 'current' => false, 'icon' => 'chevron_right'],
        ];

        static::assertSame($input, $sut->resolveTranslatedAncestors($input));
    }

    #[TestDox('resolveTranslatedAncestors() keeps original item when no translation is found')]
    public function testResolveTranslatedAncestorsKeepsOriginalWhenNoTranslation(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn(): string => 'en',
            postLanguageResolver: static fn(int $id): string => 'sv',
            translatedPostResolver: static fn(int $id, string $lang): int => 0,
        );

        $input = [
            10 => ['label' => 'Besöka', 'href' => '/besoka/', 'current' => false, 'icon' => 'chevron_right'],
        ];

        static::assertSame($input, $sut->resolveTranslatedAncestors($input));
    }

    #[TestDox('resolveTranslatedAncestors() leaves non-integer-keyed items untouched')]
    public function testResolveTranslatedAncestorsLeavesStringKeyedItemsUntouched(): void
    {
        $sut = $this->getSut(
            currentLanguageResolver: static fn(): string => 'en',
            postLanguageResolver: static fn(int $id): string => 'sv',
            translatedPostResolver: static fn(int $id, string $lang): int => 999,
        );

        $input = [
            'archive' => ['label' => 'Archive', 'href' => '/archive/', 'current' => false, 'icon' => 'chevron_right'],
        ];

        static::assertSame($input, $sut->resolveTranslatedAncestors($input));
    }

    /**
     * @param array<int, array<string, mixed>> $posts    Map of post ID to post field values.
     * @param array<int, string>               $pageLinks Map of post ID to page link URL.
     */
    private function getSut(
        ?Closure $currentLanguageResolver = null,
        ?Closure $postLanguageResolver = null,
        ?Closure $translatedPostResolver = null,
        array $posts = [],
        array $pageLinks = [],
    ): ResolveTranslatedBreadcrumbAncestors {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'getPost' => function (int $id) use ($posts): ?WP_Post {
                if (!isset($posts[$id])) {
                    return null;
                }
                $post = new WP_Post((object) $posts[$id]);
                foreach ($posts[$id] as $key => $value) {
                    $post->$key = $value;
                }
                return $post;
            },
            'getPageLink' => function (int $id) use ($pageLinks): string {
                return $pageLinks[$id] ?? '';
            },
        ]);

        return new ResolveTranslatedBreadcrumbAncestors(
            $wpService,
            $currentLanguageResolver,
            $postLanguageResolver,
            $translatedPostResolver,
        );
    }
}
