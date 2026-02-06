<?php

namespace Municipio\FullSiteEditing;

use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetOption;
use WpService\Contracts\WpAddInlineStyle;

class FullSiteEditingFeature
{
    public function __construct(
        private AddFilter&AddAction&GetOption&WpAddInlineStyle $wpService,
    ) {}

    /**
     * Enable the full site editing feature by adding necessary filters and actions.
     */
    public function enable(): void
    {
        $this->addCssVariablesToEditor();
        $this->enableGutenbergEditorForTemplates();
    }

    /**
     * Add CSS variables to the editor to allow for consistent styling across the site.
     */
    private function addCssVariablesToEditor(): void
    {
        $this->wpService->addAction('enqueue_block_assets', function () {
            $styles = $this->wpService->getOption('kirki_inline_styles');

            if (!empty($styles)) {
                // The handle is not important, as long as it is a style that can be expected to be enqueued in the editor.
                $this->wpService->wpAddInlineStyle('wp-block-library', $styles);
            }
        });
    }

    /**
     * Enable the Gutenberg editor for specific templates to allow for a more flexible editing experience.
     */
    private function enableGutenbergEditorForTemplates(): void
    {
        $this->wpService->addFilter('Municipio/Admin/Gutenberg/TemplatesToInclude', static function (array $templatesToInclude) {
            return [...$templatesToInclude, 'municipio-template'];
        });
    }
}
