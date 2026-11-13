<?php

declare(strict_types=1);

namespace Municipio\Admin\UI;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Styleguide\ComponentAssets\StylesheetManifest;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddEditorStyle;

/** Makes on-demand Styleguide CSS available to Classic and Block editors. */
class ComponentEditorStyles implements Hookable
{
    public function __construct(
        private AddAction&AddEditorStyle $wpService,
        private StylesheetManifest $stylesheets,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('after_setup_theme', [$this, 'addEditorStyles']);
    }

    public function addEditorStyles(): void
    {
        $styles = array_map(
            static fn(string $builtPath): string => 'assets/dist/styleguide/' . $builtPath,
            array_values($this->stylesheets->getStyles()),
        );
        if ($styles !== []) {
            $this->wpService->addEditorStyle($styles);
        }
    }
}
