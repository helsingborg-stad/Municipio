<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class WordPressAssetEnqueuerTest extends TestCase
{
    public function testChildAndCamelCaseSlugsUseTheirStyleguideBundle(): void
    {
        $assets = new BuiltStyleguideAssets(sys_get_temp_dir() . '/missing-styleguide-assets');

        static::assertSame(
            [
                'component-card' => 'css/components/card.css',
                'component-megamenu' => 'css/components/megamenu.css',
            ],
            $assets->pathsForComponents(['card__header' => true, 'megaMenu' => true], 'css'),
        );
    }

    public function testRenderedComponentsAndUtilitiesAreEnqueuedOnceAndPrintedInTheDocument(): void
    {
        $directory = sys_get_temp_dir() . '/municipio-component-assets-' . uniqid();
        mkdir($directory);
        file_put_contents($directory . '/manifest.json', json_encode(array_fill_keys([
            'css/components/button.css',
            'css/components/icon.css',
            'js/components/button.js',
            'css/utilities/display.css',
            'css/utilities/overflow.css',
        ], 'built')));
        file_put_contents($directory . '/utility-class-map.json', json_encode([
            'order' => ['overflow', 'display'],
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
                'wpPrintStyles' => static function (): array {
                    echo '<link id="component-assets">';
                    return [];
                },
                'wpPrintScripts' => static function (): array {
                    echo '<script id="component-assets"></script>';
                    return [];
                },
            ]);

            $assets = new WordPressAssetEnqueuer($enqueue, $wpService, $directory);
            $assets->enqueueComponent('button', [
                'sass' => ['components' => ['button', 'icon']],
                'utilities' => ['overflow'],
            ]);
            $assets->enqueueComponent('button');

            $markup = $assets->injectIntoMarkup('<html><head></head><body class="u-hidden"></body></html>');

            static::assertSame([
                'css/components/button.css',
                'css/components/icon.css',
                'css/utilities/overflow.css',
                'css/utilities/display.css',
                'js/components/button.js',
            ], $paths);
            static::assertSame([[['css-components-buttoncss', 'css-components-iconcss', 'css-utilities-overflowcss', 'css-utilities-displaycss']]], $wpService->methodCalls['wpPrintStyles']);
            static::assertSame([[['js-components-buttonjs']]], $wpService->methodCalls['wpPrintScripts']);
            static::assertStringContainsString('<link id="component-assets"></head>', $markup);
            static::assertStringContainsString('<script id="component-assets"></script></body>', $markup);
        } finally {
            unlink($directory . '/manifest.json');
            unlink($directory . '/utility-class-map.json');
            rmdir($directory);
        }
    }
}
