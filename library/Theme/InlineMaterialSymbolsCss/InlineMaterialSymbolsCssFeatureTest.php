<?php

declare(strict_types=1);

namespace Municipio\Theme\InlineMaterialSymbolsCss;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManager;
use WpUtilService\WpUtilService;

class InlineMaterialSymbolsCssFeatureTest extends TestCase
{
    private array $temporaryDirectories = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryDirectories as $temporaryDirectory) {
            $this->removeDirectory($temporaryDirectory);
        }

        $this->temporaryDirectories = [];
    }

    #[TestDox('adds hooks for frontend and admin material symbols output')]
    public function testAddHooks(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
        ]);
        $wpUtilService = $this->createMock(WpUtilService::class);
        $wpUtilService
            ->method('enqueue')
            ->willReturn(
                $this->getMockBuilder(EnqueueManager::class)->disableOriginalConstructor()->getMock(),
            );

        $feature = new InlineMaterialSymbolsCssFeature($wpService, $wpUtilService);

        $feature->addHooks();

        static::assertCount(2, $wpService->methodCalls['addAction'] ?? []);
        static::assertSame('wp_enqueue_scripts', $wpService->methodCalls['addAction'][0][0]);
        static::assertSame('admin_enqueue_scripts', $wpService->methodCalls['addAction'][1][0]);
    }

    #[TestDox('inlines the material symbols stylesheet from the built asset manifest')]
    public function testEnqueueMaterialSymbolsInlinesBuiltStylesheet(): void
    {
        $themeRoot = $this->createThemeBuild([
            'fonts/material/medium/rounded.css' => 'fonts/material/medium/rounded.hash.css',
        ], [
            'fonts/material/medium/rounded.hash.css' => '@font-face{src:url(./material-symbols-rounded.woff2) format("woff2")}',
        ]);

        $enqueueManager = $this->getMockBuilder(EnqueueManager::class)->disableOriginalConstructor()->onlyMethods(['add'])->getMock();
        $enqueueManager->expects($this->never())->method('add');

        $wpUtilService = $this->createMock(WpUtilService::class);
        $wpUtilService->method('enqueue')->willReturn($enqueueManager);

        $wpService = new FakeWpService([
            'getThemeMod' => fn(string $name): string => match ($name) {
                'icon_weight' => '400',
                'icon_style' => 'rounded',
                default => '',
            },
            'getThemeFilePath' => fn(string $file = ''): string => rtrim($themeRoot, '/') . '/' . ltrim($file, '/'),
            'getStylesheetDirectoryUri' => 'http://example.com/wp-content/themes/municipio',
            'wpRegisterStyle' => true,
            'wpAddInlineStyle' => true,
        ]);

        $feature = new InlineMaterialSymbolsCssFeature($wpService, $wpUtilService);

        $feature->enqueueMaterialSymbols();

        static::assertSame(
            [['material-symbols-medium-rounded', false]],
            $wpService->methodCalls['wpRegisterStyle'] ?? [],
        );
        static::assertSame(
            [['material-symbols-medium-rounded']],
            $wpService->methodCalls['wpEnqueueStyle'] ?? [],
        );
        static::assertSame(
            [[
                'material-symbols-medium-rounded',
                '@font-face{src:url("http://example.com/wp-content/themes/municipio/assets/dist/fonts/material/medium/material-symbols-rounded.woff2") format("woff2")}',
            ]],
            $wpService->methodCalls['wpAddInlineStyle'] ?? [],
        );
    }

    #[TestDox('falls back to external enqueue when the built stylesheet is unavailable')]
    public function testEnqueueMaterialSymbolsFallsBackToEnqueuedStylesheet(): void
    {
        $themeRoot = $this->createTemporaryDirectory();

        $enqueueManager = $this->getMockBuilder(EnqueueManager::class)->disableOriginalConstructor()->onlyMethods(['add'])->getMock();
        $enqueueManager->expects($this->once())->method('add')->with('fonts/material/medium/rounded.css')->willReturnSelf();

        $wpUtilService = $this->createMock(WpUtilService::class);
        $wpUtilService->method('enqueue')->willReturn($enqueueManager);

        $wpService = new FakeWpService([
            'getThemeMod' => fn(string $name): string => match ($name) {
                'icon_weight' => '400',
                'icon_style' => 'rounded',
                default => '',
            },
            'getThemeFilePath' => fn(string $file = ''): string => rtrim($themeRoot, '/') . '/' . ltrim($file, '/'),
        ]);

        $feature = new InlineMaterialSymbolsCssFeature($wpService, $wpUtilService);

        $feature->enqueueMaterialSymbols();

        static::assertArrayNotHasKey('wpRegisterStyle', $wpService->methodCalls);
        static::assertArrayNotHasKey('wpEnqueueStyle', $wpService->methodCalls);
        static::assertArrayNotHasKey('wpAddInlineStyle', $wpService->methodCalls);
    }

    private function createThemeBuild(array $manifest, array $assets): string
    {
        $themeRoot = $this->createTemporaryDirectory();
        $distPath = $themeRoot . '/assets/dist';

        mkdir($distPath, 0777, true);
        file_put_contents($distPath . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        foreach ($assets as $assetPath => $content) {
            $fullAssetPath = $distPath . '/' . ltrim($assetPath, '/');
            mkdir(dirname($fullAssetPath), 0777, true);
            file_put_contents($fullAssetPath, $content);
        }

        return $themeRoot;
    }

    private function createTemporaryDirectory(): string
    {
        $temporaryDirectory = sys_get_temp_dir() . '/municipio-inline-material-symbols-test-' . uniqid('', true);
        mkdir($temporaryDirectory, 0777, true);
        $this->temporaryDirectories[] = $temporaryDirectory;

        return $temporaryDirectory;
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = scandir($directory);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . '/' . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
                continue;
            }

            unlink($path);
        }

        rmdir($directory);
    }
}
