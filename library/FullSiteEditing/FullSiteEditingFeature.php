<?php

namespace Municipio\FullSiteEditing;

use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetOption;
use WpService\Contracts\WpAddInlineStyle;
use WpService\WpService;

class FullSiteEditingFeature
{
    public function __construct(
        private WpService $wpService,
    ) {}

    /**
     * Enable the full site editing feature by adding necessary filters and actions.
     */
    public function enable(): void
    {
        $this->addCssVariablesToEditor();
        $this->enableGutenbergEditorForTemplates();
        $this->disableAccessToTheTemplateEditor();
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
            return [...$templatesToInclude, 'gutenberg.blade.php'];
        });
    }

    /**
     * Disable access to the template editor to prevent users from accidentally modifying templates that are meant to be edited with the Gutenberg editor.
     */
    private function disableAccessToTheTemplateEditor(): void
    {
        $this->wpService->addAction('current_screen', function () {
            $screen = $this->wpService->getCurrentScreen();

            // Check if we are editing the 'post' post type.
            if ($screen->is_block_editor) {
                $this->wpService->removeThemeSupport('block-templates');
            }
        });
    }
}
