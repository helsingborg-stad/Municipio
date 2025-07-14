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
        $this->wpService->addAction('admin_menu', [$this, 'registerOptionsPage']);
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
            'capability'      => 'manage_options',
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
        $this->wpService->addRewriteRule(
            $this->getFrontendPageSlug(),
            'index.php?pagename=' . $this->getFrontendPageSlug(),
            'top'
        );

        $this->wpService->addFilter('query_vars', function ($vars) {
            $vars[] = 'a11y_statement';
            return $vars;
        });

        $this->wpService->addAction('template_redirect', function () {
            if (get_query_var('a11y_statement')) {
                include get_template_directory() . '/templates/a11y-statement.php';
                exit;
            }
        });
    }
}