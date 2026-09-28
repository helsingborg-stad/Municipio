<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class WordPressAssetEnqueuerTest extends TestCase
{
    public function testRenderedComponentAssetsUseWpUtilEnqueue(): void
    {
        $directory = sys_get_temp_dir() . '/municipio-component-assets-' . uniqid();
        mkdir($directory);
        file_put_contents($directory . '/manifest.json', json_encode(array_fill_keys([
            'css/components/button.css',
            'css/components/icon.css',
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

            $assets = new WordPressAssetEnqueuer($enqueue, $wpService, $directory);
            static::assertSame('<link>', $assets->renderStyles(''));
            static::assertSame(['css/utilities/preloader.css'], $paths);
            $paths = [];

            $assets->enqueueComponent('button', ['sass' => ['components' => ['icon']]]);
            $assets->enqueueComponent('button');

            static::assertSame('<link>', $assets->renderStyles('<div class="u-hidden"></div>'));
            static::assertSame('<script></script>', $assets->renderScripts());
            static::assertSame([
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
