<?php

declare(strict_types=1);

namespace Municipio\Admin\UI;

use Municipio\Styleguide\ComponentAssets\StylesheetManifest;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ComponentEditorStylesTest extends TestCase
{
    public function testUsesTheSameManifestForClassicAndBlockEditors(): void
    {
        $wpService = new FakeWpService();
        $manifest = $this->createMock(StylesheetManifest::class);
        $manifest
            ->method('getStyles')
            ->willReturn([
                'css/components/card.css' => 'css/components/card.abc.css',
                'css/utilities/color.css' => 'css/utilities/color.def.css',
            ]);
        $styles = new ComponentEditorStyles($wpService, $manifest);
        $styles->addEditorStyles();

        static::assertSame(
            [
                'assets/dist/styleguide/css/components/card.abc.css',
                'assets/dist/styleguide/css/utilities/color.def.css',
            ],
            $wpService->methodCalls['addEditorStyle'][0][0],
        );
    }

    public function testDoesNotEnqueueContentStylesInTheAdminDocument(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $manifest = $this->createMock(StylesheetManifest::class);
        $manifest->expects(static::never())->method('getStyles');
        (new ComponentEditorStyles($wpService, $manifest))->addHooks();
        static::assertSame(['after_setup_theme'], array_column($wpService->methodCalls['addAction'], 0));
    }
}
