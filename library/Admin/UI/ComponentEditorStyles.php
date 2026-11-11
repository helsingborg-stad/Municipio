<?php

declare(strict_types=1);

namespace Municipio\Admin\UI;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Styleguide\ComponentAssets\StylesheetManifest;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddEditorStyle;
use WpService\Contracts\GetTemplateDirectoryUri;
use WpService\Contracts\IsAdmin;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

/** Makes on-demand Styleguide CSS available to Classic and Block editors. */
class ComponentEditorStyles implements Hookable
{
    public function __construct(
        private AddAction&AddEditorStyle&GetTemplateDirectoryUri&IsAdmin $wpService,
        private EnqueueManagerInterface $enqueue,
        private StylesheetManifest $stylesheets,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('after_setup_theme', [$this, 'addClassicEditorStyles']);
        $this->wpService->addAction('enqueue_block_assets', [$this, 'enqueueBlockEditorStyles']);
    }

    public function addClassicEditorStyles(): void
    {
        $styles = array_map(
            fn(string $builtPath): string => $this->wpService->getTemplateDirectoryUri() . '/assets/dist/styleguide/' . $builtPath,
            array_values($this->stylesheets->getStyles()),
        );
        if ($styles !== []) {
            $this->wpService->addEditorStyle($styles);
        }
    }

    public function enqueueBlockEditorStyles(): void
    {
        if (!$this->wpService->isAdmin()) {
            return;
        }
        foreach ($this->stylesheets->getStyles() as $builtPath) {
            $this->enqueue->add($builtPath);
        }
    }
}
