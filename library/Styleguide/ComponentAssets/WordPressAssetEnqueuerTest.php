<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

use HelsingborgStad\BladeService\BladeService;
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

    public function testRenderedComponentsAndUtilitiesAreEnqueuedOnceForBladeStacks(): void
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

            $styleTags = $assets->renderStyles('<body class="u-hidden"></body>');
            $scriptTags = $assets->renderScripts();

            static::assertSame([
                'css/components/button.css',
                'css/components/icon.css',
                'css/utilities/overflow.css',
                'css/utilities/display.css',
                'js/components/button.js',
            ], $paths);
            static::assertSame([[['css-components-buttoncss', 'css-components-iconcss', 'css-utilities-overflowcss', 'css-utilities-displaycss']]], $wpService->methodCalls['wpPrintStyles']);
            static::assertSame([[['js-components-buttonjs']]], $wpService->methodCalls['wpPrintScripts']);
            static::assertSame('<link id="component-assets">', $styleTags);
            static::assertSame('<script id="component-assets"></script>', $scriptTags);
        } finally {
            unlink($directory . '/manifest.json');
            unlink($directory . '/utility-class-map.json');
            rmdir($directory);
        }
    }

    public function testBladePushesAssetsIntoHeadAndBodyStacks(): void
    {
        $directory = sys_get_temp_dir() . '/municipio-blade-assets-' . uniqid();
        mkdir($directory);
        file_put_contents($directory . '/manifest.json', json_encode([
            'css/components/button.css' => 'css/components/button.css',
            'js/components/button.js' => 'js/components/button.js',
        ]));
        file_put_contents($directory . '/layout.blade.php', <<<'BLADE'
@section('body-content')
    @php(\Municipio\Styleguide\ComponentAssets\WordPressAssetEnqueuer::instance()->enqueueComponent('button'))
    <div>Body</div>
@stop
@include('templates.sections.component-assets')
<head>@stack('styles')</head>
<body>@yield('body-content')@stack('scripts')</body>
BLADE);

        try {
            $enqueue = $this->createMock(EnqueueManagerInterface::class);
            $enqueue->method('add')->willReturnSelf();
            $wpService = new FakeWpService([
                'wpPrintStyles' => static function (): array {
                    echo '<link id="component-style">';
                    return [];
                },
                'wpPrintScripts' => static function (): array {
                    echo '<script id="component-script"></script>';
                    return [];
                },
            ]);
            WordPressAssetEnqueuer::setInstance(new WordPressAssetEnqueuer($enqueue, $wpService, $directory));
            $blade = new BladeService([$directory, dirname(__DIR__, 3) . '/views/v3']);

            $markup = $blade->makeView('layout')->render();

            static::assertMatchesRegularExpression('/<head>.*<link id="component-style">.*<\/head>/s', $markup);
            static::assertMatchesRegularExpression('/<body>.*<script id="component-script"><\/script>.*<\/body>/s', $markup);
        } finally {
            WordPressAssetEnqueuer::setInstance(null);
            unlink($directory . '/manifest.json');
            unlink($directory . '/layout.blade.php');
            rmdir($directory);
        }
    }
}
