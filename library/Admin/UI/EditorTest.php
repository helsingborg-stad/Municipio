<?php

declare(strict_types=1);

namespace Municipio\Admin\UI;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class EditorTest extends TestCase
{
    public function testRegistersPresentationStylesOnlyAsEditorContent(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
            'addFilter' => true,
            'getTemplateDirectoryUri' => 'https://example.org/theme',
            'applyFilters' => static fn($hook, $value) => $value,
        ]);
        $enqueue = $this->createMock(EnqueueManagerInterface::class);
        $enqueue->expects(static::never())->method('add');
        $editor = new Editor($wpService, $enqueue);
        $editor->editorStyle();

        static::assertNotContains('enqueue_block_assets', array_column($wpService->methodCalls['addAction'], 0));
        static::assertCount(5, $wpService->methodCalls['addEditorStyle']);
        foreach ($wpService->methodCalls['addEditorStyle'] as $call) {
            static::assertStringStartsWith('assets/dist/', $call[0]);
        }
        static::assertArrayNotHasKey('wpEnqueueStyle', $wpService->methodCalls);
    }

    public function testPreservesExternalEditorStyleOverrides(): void
    {
        $wpService = new FakeWpService([
            'addAction' => true,
            'addFilter' => true,
            'getTemplateDirectoryUri' => 'https://example.org/theme',
            'applyFilters' => static fn($hook, $value) => $hook === 'Municipio/Admin/BlockEditorMunicipio' ? 'https://example.org/custom.css' : $value,
        ]);
        $editor = new Editor($wpService, $this->createMock(EnqueueManagerInterface::class));
        $editor->editorStyle();

        static::assertSame('https://example.org/custom.css', $wpService->methodCalls['addEditorStyle'][4][0]);
    }
}
