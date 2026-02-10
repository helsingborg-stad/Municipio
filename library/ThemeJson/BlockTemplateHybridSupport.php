<?php

namespace Municipio\ThemeJson;

use WpService\WpService;

/**
 * Enables block theme features (like Global Styles) while keeping PHP template rendering.
 *
 * This class allows the theme to be recognized as a block theme by WordPress,
 * enabling the Site Editor's Global Styles panel, while still using the existing
 * PHP/Blade template system for actual page rendering.
 */
class BlockTemplateHybridSupport
{
    public function __construct(private WpService $wpService)
    {
        // Prevent block templates from being used for rendering - keep using PHP templates
        $this->wpService->addFilter('template_include', [$this, 'usePhpTemplates'], 99);

        // Indicate that this theme supports block templates (enables Site Editor features)
        $this->wpService->addAction('after_setup_theme', [$this, 'addBlockThemeSupport'], 5);
    }

    /**
     * Ensure PHP templates are used instead of block templates.
     *
     * When WordPress finds a matching block template, it would normally use it.
     * This filter intercepts that and returns the original PHP template path.
     *
     * @param string $template The template path WordPress wants to use
     * @return string The PHP template path to use instead
     */
    public function usePhpTemplates(string $template): string
    {
        // If WordPress resolved to a block template (.html), redirect to PHP template
        if (str_ends_with($template, '.html')) {
            // Let WordPress find the appropriate PHP template
            $phpTemplate = $this->findPhpTemplate();
            if ($phpTemplate) {
                return $phpTemplate;
            }
        }

        return $template;
    }

    /**
     * Find the appropriate PHP template based on WordPress template hierarchy.
     *
     * @return string|null PHP template path or null if not found
     */
    private function findPhpTemplate(): ?string
    {
        // Get the template from the standard WordPress hierarchy
        // This checks for index.php in the theme
        $templates = ['index.php'];

        foreach ($templates as $templateFile) {
            $path = get_template_directory() . '/' . $templateFile;
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Add theme support for block templates.
     *
     * This tells WordPress that the theme supports block templates,
     * which enables the Site Editor's Global Styles panel.
     */
    public function addBlockThemeSupport(): void
    {
        // Enable block template parts - allows Global Styles editing
        $this->wpService->addThemeSupport('block-template-parts');
    }
}
