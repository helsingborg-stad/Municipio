<?php

namespace Municipio\A11yStatement;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class A11yStatement implements Hookable
{
    public function __construct(private WpService $wpService, private AcfService $acfService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerOptionsPage']);
        $this->wpService->addAction('init', [$this, 'registerFrontendPage']);
    }

    /**
     * Register the options page for the accessibility statement.
     * 
     * @return void
     */
    public function registerOptionsPage(): void
    {
        $this->acfService->addOptionsPage([
            'page_title'      => __('Accessibility Statement', 'municipio'),
            'menu_title'      => __('Accessibility Statement', 'municipio'),
            'menu_slug'       => 'a11ystatement',
            'capability'      => 'edit_posts',
            'redirect'        => true,
            'update_button'   => __('Update', 'municipio'),
            'updated_message' => __('The accessibility statement has been updated.', 'municipio'),
            'parent_slug'     => 'options-general.php',
        ]);
    }

    /**
     * Get the slug for the frontend accessibility statement page.
     *
     * @return string
     */
    private function getFrontendPageSlug(): string
    {
        return __('accessibility-statement', 'municipio');
    }

    /**
     * Register the frontend page for the accessibility statement.
     * 
     * @return void
     */
    public function registerFrontendPage() : void
    {
        $slug = $this->getFrontendPageSlug();

        // Add rewrite rule
        $this->wpService->addRewriteRule(
            '^' . $slug . '/?$',
            'index.php?a11y_statement=1',
            'top'
        );

        // Register query var
        $this->wpService->addFilter('query_vars', function ($vars) {
            $vars[] = 'a11y_statement';
            return $vars;
        });

        // Use template_include to render a real template
        $this->wpService->addFilter('template_include', function ($template) {
            if (get_query_var('a11y_statement')) {
                return 'a11y';
            }
            return $template;
        });

        // Auto-flush rewrite rules if our custom rule isn't present
        $this->wpService->addAction('wp_loaded', function () use ($slug) {
            $rules = get_option('rewrite_rules');
            $expectedRule = '^' . $slug . '/?$';

            if (!isset($rules[$expectedRule])) {
                flush_rewrite_rules();
            }
        });
    }
}