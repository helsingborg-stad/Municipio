<?php

namespace Municipio\A11yStatement;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetQueryVar;
use WpService\Contracts\__;
use WpService\Contracts\AddFilter;
use WpService\Contracts\AddRewriteRule;
use WpService\Contracts\FlushRewriteRules;
use WpService\Contracts\GetOption;
use WpService\Contracts\HomeUrl;

class A11yStatement implements Hookable
{
    public function __construct(
        private AddAction&GetQueryVar&AddFilter&AddRewriteRule&FlushRewriteRules&GetOption&HomeUrl&__ $wpService, 
        private AcfService $acfService
    ){}

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerOptionsPage']);
        $this->wpService->addAction('init', [$this, 'registerFrontendPage']);
        $this->wpService->addFilter('acf/load_field/key=field_689c4df0b4e2e', [$this, 'loadA11yStatementUrlField']);
        $this->wpService->addAction('wp_head', [$this, 'addSchemaTag']);
    }

    /**
     * Add schema tag for the accessibility statement.
     *
     * @return void
     */
    public function addSchemaTag(): void
    {
        $schemaData = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'accessibilitySummary' => $this->getFullStatementUrl(),
            'name' => $this->wpService->__('Accessibility Statement', 'municipio'),
            'description' => $this->wpService->__('This is the accessibility statement for our website.', 'municipio'),
        ];
        echo '<script type="application/ld+json">' . json_encode($schemaData) . '</script>';
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
     * Replace url in the A11y Statement URL field.
     *
     * @param array $field
     * @return array
     */
    public function loadA11yStatementUrlField(array $field): array
    {
        $field['message'] = str_replace(
            '{{a11y_page_url}}',
            $this->getFullStatementUrl(),
            $field['message']
        );

        return $field;
    }

    /**
     * Get the slug for the frontend accessibility statement page.
     *
     * @return string
     */
    private function getFrontendPageSlug(): string
    {
        return $this->wpService->__('accessibility-statement', 'municipio');
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
            if ($this->wpService->getQueryVar('a11y_statement')) {
                return 'a11y';
            }
            return $template;
        });

        // Auto-flush rewrite rules if our custom rule isn't present
        $this->wpService->addAction('wp_loaded', function () use ($slug) {
            $rules          = $this->wpService->getOption('rewrite_rules');
            $expectedRule   = '^' . $slug . '/?$';
            if (!isset($rules[$expectedRule])) {
                $this->wpService->flushRewriteRules();
            }
        });
    }

    /**
     * Get the full URL for the accessibility statement.
     *
     * @return string
     */
    private function getFullStatementUrl(): string
    {
        return $this->wpService->homeUrl(
            $this->getFrontendPageSlug()
        );
    }
}