<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveTranslatedBreadcrumbItemsTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolveTranslatedBreadcrumbItems::class, $this->getSut());
    }

    #[TestDox('addHooks() registers the breadcrumb items filter')]
    public function testAddHooksRegistersBreadcrumbItemsFilter(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
            'getTheTitle' => '',
            'getPermalink' => '',
        ]);

        $sut = new ResolveTranslatedBreadcrumbItems($wpService);

        $sut->addHooks();

        static::assertSame(
            [['Municipio/Breadcrumbs/Items', [$sut, 'resolveTranslatedBreadcrumbItems'], 10, 3]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('resolveTranslatedBreadcrumbItems() replaces translated breadcrumb labels and URLs')]
    public function testResolveTranslatedBreadcrumbItemsReplacesTranslatedLabelsAndUrls(): void
    {
        $items = [
            2 => [
                'label' => 'Home',
                'href' => 'https://example.com',
                'icon' => 'home',
            ],
            15 => [
                'label' => '',
                'href' => 'https://example.com/en/parent',
                'icon' => 'chevron_right',
            ],
            25 => [
                'label' => 'English child',
                'href' => 'https://example.com/en/child',
                'icon' => 'chevron_right',
            ],
        ];

        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => 'sv',
            postTranslationsResolver: static function (int $postId): array {
                return match ($postId) {
                    15 => ['en' => 15, 'sv' => 115],
                    25 => ['en' => 25, 'sv' => 125],
                    default => [],
                };
            }
        );

        $resolvedItems = $sut->resolveTranslatedBreadcrumbItems($items);

        static::assertSame('Home', $resolvedItems[2]['label']);
        static::assertSame('Foralder', $resolvedItems[15]['label']);
        static::assertSame('https://example.com/sv/foralder', $resolvedItems[15]['href']);
        static::assertSame('Barn', $resolvedItems[25]['label']);
        static::assertSame('https://example.com/sv/barn', $resolvedItems[25]['href']);
    }

    #[TestDox('resolveTranslatedBreadcrumbItems() returns original items when no current language exists')]
    public function testResolveTranslatedBreadcrumbItemsReturnsOriginalItemsWithoutCurrentLanguage(): void
    {
        $items = [
            15 => [
                'label' => '',
                'href' => 'https://example.com/en/parent',
                'icon' => 'chevron_right',
            ],
        ];

        $sut = $this->getSut(
            currentLanguageResolver: static fn (): string => '',
            postTranslationsResolver: static fn (int $postId): array => ['en' => $postId, 'sv' => 115]
        );

        static::assertSame($items, $sut->resolveTranslatedBreadcrumbItems($items));
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $currentLanguageResolver Optional current language resolver.
     * @param ?Closure $postTranslationsResolver Optional post translations resolver.
     *
     * @return ResolveTranslatedBreadcrumbItems
     */
    private function getSut(
        ?Closure $currentLanguageResolver = null,
        ?Closure $postTranslationsResolver = null
    ): ResolveTranslatedBreadcrumbItems {
        return new ResolveTranslatedBreadcrumbItems(
            new FakeWpService([
                'addFilter' => true,
                'getTheTitle' => static function (int $postId): string {
                    return match ($postId) {
                        115 => 'Foralder',
                        125 => 'Barn',
                        default => '',
                    };
                },
                'getPermalink' => static function (int $postId): string {
                    return match ($postId) {
                        115 => 'https://example.com/sv/foralder',
                        125 => 'https://example.com/sv/barn',
                        default => '',
                    };
                },
            ]),
            $currentLanguageResolver,
            $postTranslationsResolver
        );
    }
}