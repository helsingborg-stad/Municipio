<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class WordPressAssetEnqueuerTest extends TestCase
{
    public function testFragmentContextCapturesAndRestoresComponentRegistrations(): void
    {
        $enqueue = $this->createMock(EnqueueManagerInterface::class);
        $wpService = new FakeWpService();
        $source = new WordPressAssetEnqueuer($enqueue, $wpService, sys_get_temp_dir());
        $target = new WordPressAssetEnqueuer($enqueue, $wpService, sys_get_temp_dir());

        $source->beginFragment();
        $source->beginFragment();
        $source->enqueueComponent('collection', ['sass' => ['components' => ['icon']]]);
        $source->enqueueUtility('display');
        $source->enqueueStyle('custom', 'css/custom.css');
        $source->enqueueScript('custom', 'js/custom.js');
        $context = $source->endFragment();
        static::assertSame($context, $source->endFragment());

        $target->beginFragment();
        $target->restoreFragment($context);
        static::assertSame($context, $target->endFragment());
        static::assertSame([], $source->endFragment());
    }

    public function testRenderedComponentAssetsUseWpUtilEnqueue(): void
    {
        $directory = sys_get_temp_dir() . '/municipio-component-assets-' . uniqid();
        mkdir($directory);
        file_put_contents($directory . '/manifest.json', json_encode(array_fill_keys([
            'css/components/button.css',
            'css/components/icon.css',
            'css/components/table.css',
            'css/components/card.css',
            'js/components/button.js',
            'css/utilities/display.css',
            'css/utilities/preloader.css',
        ], 'built')));
        file_put_contents($directory . '/utility-class-map.json', json_encode([
            'order' => ['display', 'preloader'],
            'classes' => ['u-hidden' => ['display']],
        ]));

        try {
            $paths = [];
            $enqueue = $this->createMock(EnqueueManagerInterface::class);
            $enqueue->method('add')->willReturnCallback(
                static function (string $path) use (&$paths, $enqueue): EnqueueManagerInterface {
                    $paths[] = $path;
                    return $enqueue;
                },
            );
            $wpService = new FakeWpService([
                'wpPrintStyles' => static function (): array { echo '<link>'; return []; },
                'wpPrintScripts' => static function (): array { echo '<script></script>'; return []; },
            ]);

            $customDetector = new class implements MarkupDetectorInterface {
                public function matches(string $markup): bool
                {
                    return str_contains($markup, 'data-custom');
                }

                public function styles(): array
                {
                    return ['custom-card' => 'css/components/card.css'];
                }
            };

            $assets = new WordPressAssetEnqueuer($enqueue, $wpService, $directory, [$customDetector]);
            static::assertSame('<link>', $assets->renderStyles(''));
            static::assertSame(['css/utilities/preloader.css'], $paths);
            $paths = [];

            $assets->enqueueComponent('button', ['sass' => ['components' => ['icon']]]);
            $assets->enqueueComponent('button');

            static::assertSame('<link>', $assets->renderStyles('<div class="u-hidden" data-custom><table border="1"><tr><td>Value</td></tr></table></div>'));
            static::assertSame('<script></script>', $assets->renderScripts());
            static::assertSame([
                'css/components/table.css',
                'css/components/card.css',
                'css/components/icon.css',
                'css/components/button.css',
                'css/utilities/display.css',
                'css/utilities/preloader.css',
                'js/components/button.js',
            ], $paths);
        } finally {
            unlink($directory . '/manifest.json');
            unlink($directory . '/utility-class-map.json');
            rmdir($directory);
        }
    }
}
