<?php

declare(strict_types=1);

namespace Municipio\Head;

use AcfService\Implementations\FakeAcfService;
use ComponentLibrary\Integrations\Image\ImageResolverInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class HeroImagePreloadResolverTest extends TestCase
{
    protected function setUp(): void
    {
        \Modularity\App::$display = (object) [
            'modules' => [],
            'options' => [],
        ];
    }

    #[TestDox('resolve() returns preload attributes for a hero module in the hero sidebar')]
    public function testResolveReturnsPreloadAttributesForHeroModule(): void
    {
        \Modularity\App::$display = (object) [
            'modules' => [
                'slider-area' => [
                    'modules' => [
                        (object) [
                            'ID' => 101,
                            'post_type' => 'mod-hero',
                        ],
                    ],
                ],
            ],
            'options' => [],
        ];

        $sut = new HeroImagePreloadResolver(
            new HeroImageModuleProvider(
                new HeroSidebarModuleProvider(),
                new HeroWidgetModuleProvider(new FakeWpService(['getOption' => []]))
            ),
            new HeroImageContractResolver(
                new FakeAcfService([
                    'getFields' => [
                        'mod_hero_background_type' => 'image',
                        'mod_hero_background_image' => ['id' => 77],
                    ],
                ]),
                $this->getImageResolverStub()
            )
        );

        static::assertSame(
            [
                'href' => 'https://example.com/77-1920xauto.jpg',
                'imagesrcset' => implode(', ', [
                    'https://example.com/77-425x0.jpg 425w',
                    'https://example.com/77-768x0.jpg 768w',
                    'https://example.com/77-1024x0.jpg 1024w',
                    'https://example.com/77-1440x0.jpg 1440w',
                    'https://example.com/77-1680x0.jpg 1680w',
                    'https://example.com/77-1920x0.jpg 1920w',
                ]),
                'imagesizes' => '100vw',
                'fetchpriority' => 'high',
            ],
            $sut->resolve()
        );
    }

    #[TestDox('resolve() returns preload attributes for a widget-backed slider module in the hero sidebar')]
    public function testResolveReturnsPreloadAttributesForWidgetBackedSliderModule(): void
    {
        $wpService = new FakeWpService([
            'getOption' => static fn(string $option, mixed $default = false): mixed => match ($option) {
                'sidebars_widgets' => [
                    'slider-area' => ['modularity-module-2'],
                ],
                'widget_modularity-module' => [
                    2 => ['module_id' => 202],
                ],
                default => $default,
            },
            'getPosts' => [
                (object) [
                    'ID' => 202,
                    'post_type' => 'mod-slider',
                ],
            ],
            'isUserLoggedIn' => false,
        ]);

        $sut = new HeroImagePreloadResolver(
            new HeroImageModuleProvider(
                new HeroSidebarModuleProvider(),
                new HeroWidgetModuleProvider($wpService)
            ),
            new HeroImageContractResolver(
                new FakeAcfService([
                    'getFields' => [
                        'slides' => [
                            [
                                'acf_fc_layout' => 'image',
                                'image' => ['id' => 88],
                            ],
                        ],
                    ],
                ]),
                $this->getImageResolverStub()
            )
        );

        static::assertSame('https://example.com/88-1920xauto.jpg', $sut->resolve()['href'] ?? null);
    }

    #[TestDox('resolve() skips hidden hero modules and uses the next eligible image source')]
    public function testResolveSkipsHiddenModules(): void
    {
        \Modularity\App::$display = (object) [
            'modules' => [
                'slider-area' => [
                    'modules' => [
                        (object) [
                            'ID' => 101,
                            'post_type' => 'mod-hero',
                            'hidden' => 'true',
                        ],
                        (object) [
                            'ID' => 102,
                            'post_type' => 'mod-hero',
                        ],
                    ],
                ],
            ],
            'options' => [],
        ];

        $sut = new HeroImagePreloadResolver(
            new HeroImageModuleProvider(
                new HeroSidebarModuleProvider(),
                new HeroWidgetModuleProvider(new FakeWpService(['getOption' => []]))
            ),
            new HeroImageContractResolver(
                new FakeAcfService([
                    'getFields' => static fn(int $postId): array => match ($postId) {
                        101 => [
                            'mod_hero_background_type' => 'image',
                            'mod_hero_background_image' => ['id' => 77],
                        ],
                        102 => [
                            'mod_hero_background_type' => 'image',
                            'mod_hero_background_image' => ['id' => 99],
                        ],
                        default => [],
                    },
                ]),
                $this->getImageResolverStub()
            )
        );

        static::assertSame('https://example.com/99-1920xauto.jpg', $sut->resolve()['href'] ?? null);
    }

    /**
     * Create a deterministic image resolver for tests.
     *
     * @return ImageResolverInterface
     */
    private function getImageResolverStub(): ImageResolverInterface
    {
        return new class () implements ImageResolverInterface {
            public function getImageUrl(int $id, array $size): ?string
            {
                $width = $size[0] ?? 'unknown';
                $height = $size[1] === false ? 'auto' : (string) ($size[1] ?? 'auto');

                return sprintf('https://example.com/%d-%sx%s.jpg', $id, $width, $height);
            }

            public function getImageAltText(int $id): ?string
            {
                return sprintf('Image %d', $id);
            }
        };
    }
}
