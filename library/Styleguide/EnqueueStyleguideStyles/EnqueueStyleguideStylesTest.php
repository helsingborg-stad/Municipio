<?php

namespace Municipio\Styleguide\EnqueueStyleguideStyles;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddEditorStyle;
use WpService\Contracts\GetTemplateDirectoryUri;
use WpService\Contracts\WpEnqueueStyle;

class EnqueueStyleguideStylesTest extends TestCase
{
    #[TestDox('attaches to required hooks')]
    public function testAttachesToRequiredHooks(): void
    {
        $wpService = static::createWpService();
        $enqueueStyleguideStyles = new EnqueueStyleguideStyles($wpService);

        $enqueueStyleguideStyles->addHooks();

        static::assertContains('wp_enqueue_scripts', $wpService->addedActions);
        static::assertContains('login_enqueue_scripts', $wpService->addedActions);
        static::assertContains('enqueue_block_editor_assets', $wpService->addedActions);
    }

    #[TestDox('enqueue stylesheet')]
    public function testOutputsCorrectCssPathForFrontendAndLogin(): void
    {
        $wpService = static::createWpService();
        $enqueueStyleguideStyles = new EnqueueStyleguideStyles($wpService);

        $enqueueStyleguideStyles->outputStyleguideCss();

        static::assertSame(1, $wpService->enqueuedStylesCount);
    }

    #[TestDox('adds stylesheet to editor')]
    public function testOutputsCorrectCssPathForEditor(): void
    {
        $wpService = static::createWpService();
        $enqueueStyleguideStyles = new EnqueueStyleguideStyles($wpService);

        $enqueueStyleguideStyles->outputStyleguideCss(true);

        static::assertSame(1, $wpService->addedEditorStylesCount);
    }

    private static function createWpService(): AddAction&WpEnqueueStyle&GetTemplateDirectoryUri&AddEditorStyle
    {
        return new class implements AddAction, WpEnqueueStyle, GetTemplateDirectoryUri, AddEditorStyle {
            public array $addedActions = [];
            public int $enqueuedStylesCount = 0;
            public int $addedEditorStylesCount = 0;

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addedActions[] = $hookName;
                return true;
            }

            public function wpEnqueueStyle(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, string $media = 'all'): void
            {
                $this->enqueuedStylesCount++;
            }

            public function getTemplateDirectoryUri(): string
            {
                return 'http://example.com/wp-content/themes/municipio';
            }

            public function addEditorStyle(array|string $stylesheet = 'editor-style.css'): void
            {
                $this->addedEditorStylesCount++;
            }
        };
    }
}
