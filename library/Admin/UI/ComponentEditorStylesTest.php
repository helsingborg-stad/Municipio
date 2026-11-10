<?php

declare(strict_types=1);

namespace Municipio\Admin\UI;

use Municipio\Styleguide\ComponentAssets\StylesheetManifest;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class ComponentEditorStylesTest extends TestCase
{
    public function testUsesTheSameManifestForClassicAndBlockEditors(): void
    {
        $wpService = new FakeWpService(['isAdmin' => true, 'getTemplateDirectoryUri' => 'https://example.org/theme']);
        $manifest = $this->createMock(StylesheetManifest::class);
        $manifest
            ->method('getStyles')
            ->willReturn([
                'css/components/card.css' => 'css/components/card.abc.css',
                'css/utilities/color.css' => 'css/utilities/color.def.css',
            ]);
        $enqueue = $this->createMock(EnqueueManagerInterface::class);
        $enqueuedPaths = [];
        $enqueue
            ->expects(static::exactly(2))
            ->method('add')
            ->willReturnCallback(static function (string $path) use ($enqueue, &$enqueuedPaths): EnqueueManagerInterface {
                $enqueuedPaths[] = $path;
                return $enqueue;
            });
        $styles = new ComponentEditorStyles($wpService, $enqueue, $manifest);
        $styles->addClassicEditorStyles();
        $styles->enqueueBlockEditorStyles();

        static::assertSame(['css/components/card.abc.css', 'css/utilities/color.def.css'], $enqueuedPaths);
        static::assertSame(
            [
                'https://example.org/theme/assets/dist/styleguide/css/components/card.abc.css',
                'https://example.org/theme/assets/dist/styleguide/css/utilities/color.def.css',
            ],
            $wpService->methodCalls['addEditorStyle'][0][0],
        );
    }

    public function testDoesNotLoadTheEntireComponentLibraryOnTheFrontend(): void
    {
        $wpService = new FakeWpService(['isAdmin' => false]);
        $manifest = $this->createMock(StylesheetManifest::class);
        $manifest->expects(static::never())->method('getStyles');
        $enqueue = $this->createMock(EnqueueManagerInterface::class);
        $enqueue->expects(static::never())->method('add');
        (new ComponentEditorStyles($wpService, $enqueue, $manifest))->enqueueBlockEditorStyles();
    }
}
