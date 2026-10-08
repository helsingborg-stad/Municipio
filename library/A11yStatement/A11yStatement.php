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
use WpService\Contracts\RegisterPostType;

class A11yStatement implements Hookable
{
    private const KNOWN_ISSUE_CATEGORY_ORDER = [
        'vision',
        'color',
        'mobility',
        'motor',
        'hearing',
        'cognitive',
        'other',
    ];

    public function __construct(
        private AddAction&GetQueryVar&AddFilter&AddRewriteRule&FlushRewriteRules&GetOption&HomeUrl&RegisterPostType&__ $wpService,
        private AcfService $acfService
    ){}

    public function addHooks(): void
    {
        $this->wpService->addFilter('option_modularity-options', [$this, 'enableFrontendFormModule']);
        $this->wpService->addAction('init', [$this, 'registerOptionsPage']);
        $this->wpService->addAction('init', [$this, 'registerFrontendPage']);
        $this->wpService->addAction('init', [$this, 'registerReportPostType'], 5);
        $this->wpService->addAction('init', [$this, 'ensureReportFormModule'], 20);
        $this->wpService->addAction('acf/save_post', [$this, 'sortKnownIssues'], 5);
        $this->wpService->addAction('acf/save_post', [$this, 'syncReportFormModule'], 20);
        $this->wpService->addAction('pre_get_posts', [$this, 'hideReportFormModuleFromAdmin']);
        $this->wpService->addAction('admin_notices', [$this, 'displayReportFormAdminNotice']);
        $this->wpService->addFilter('acf/load_field/key=field_689c4df0b4e2e', [$this, 'loadA11yStatementUrlField']);
        $this->wpService->addAction('wp_head', [$this, 'addSchemaTag']);
        $this->wpService->addFilter('Modularity/hasModule', [$this, 'loadReportFormAssets'], 10, 2);
    }

    /**
     * Enable Frontend Form at runtime when its plugin is active.
     *
     * The form is fully configured by this feature and is not intended to be
     * manually enabled or managed through Modularity's module settings.
     *
     * @param mixed $options
     * @return array<string, mixed>
     */
    public function enableFrontendFormModule(mixed $options): array
    {
        $options = is_array($options) ? $options : [];

        if (!class_exists('ModularityFrontendForm\\Module\\FrontendForm')) {
            return $options;
        }

        $enabledModules = (array) ($options['enabled-modules'] ?? []);
        if (!in_array('mod-frontend-form', $enabledModules, true)) {
            $enabledModules[] = 'mod-frontend-form';
        }

        $options['enabled-modules'] = $enabledModules;

        return $options;
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
            'page_title'      => $this->wpService->__('Accessibility Statement', 'municipio'),
            'menu_title'      => $this->wpService->__('Accessibility Statement', 'municipio'),
            'menu_slug'       => 'a11ystatement',
            'capability'      => 'edit_posts',
            'redirect'        => true,
            'update_button'   => $this->wpService->__('Update', 'municipio'),
            'updated_message' => $this->wpService->__('The accessibility statement has been updated.', 'municipio'),
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
        //Enabled
        if ($this->acfService->getField('mun_a11ystatement_enabled', 'options') === false) {
            return;
        }

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

        //Template bypass
        $this->wpService->addFilter('Municipio/Template/MayBeCustomTemplateRequest', function ($mayBeCustomTemplateRequest) use ($slug) {
            if ($this->wpService->getQueryVar('a11y_statement')) {
                return true;
            }
            return $mayBeCustomTemplateRequest;
        });
    }

    /**
     * Creates and configures the Frontend Form module used to report accessibility issues.
     *
     * The module only activates its mail handler. No database handler is configured, so
     * submissions are never stored as WordPress posts.
     */
    public function ensureReportFormModule(): void
    {
        if (!$this->isFrontendFormAvailable()) {
            return;
        }

        $email = $this->getReportRecipientEmail();
        if ($email === null) {
            return;
        }

        $moduleId = (int) get_option('mun_a11ystatement_report_form_id', 0);
        if ($moduleId <= 0 || get_post_type($moduleId) !== 'mod-frontend-form') {
            $moduleId = wp_insert_post([
                'post_title'  => $this->wpService->__('Report accessibility issue', 'municipio'),
                'post_status' => 'publish',
                'post_type'   => 'mod-frontend-form',
            ]);

            if (is_wp_error($moduleId) || !$moduleId) {
                return;
            }

            update_option('mun_a11ystatement_report_form_id', (int) $moduleId, false);
        }

        $moduleTitle = $this->wpService->__('Report accessibility issue', 'municipio');
        if (get_post_field('post_title', $moduleId) !== $moduleTitle) {
            wp_update_post([
                'ID'         => $moduleId,
                'post_title' => $moduleTitle,
            ]);
        }

        update_field('saveToPostType', 'mun_a11y_report', $moduleId);
        update_field('formSteps', [
            [
                'formStepTitle'   => $this->wpService->__('Describe accessibility issue', 'municipio'),
                'formStepContent' => $this->wpService->__('Tell us if something does not work or is difficult to use. We will address the issue as soon as possible.', 'municipio'),
                'formStepGroup'   => ['group_mun_a11ystatement_report_form'],
            ],
            [
                'formStepTitle'   => $this->wpService->__('Contact details', 'municipio'),
                'formStepContent' => $this->wpService->__('Please provide your contact details if you would like us to contact you with questions about the reported issue.', 'municipio'),
                'formStepGroup'   => ['group_mun_a11ystatement_report_contact'],
            ],
        ], $moduleId);
        update_field('activeHandlers', ['MailHandler'], $moduleId);
        update_field('MailHandlerConfig', [
            'Recivers' => [['Email' => $email]],
        ], $moduleId);
        update_post_meta($moduleId, 'modularity-module-hide-title', 0);
        update_post_meta($moduleId, 'mun_a11ystatement_managed_form', 1);
    }

    /**
     * Register the internal post type that supplies the fields to Frontend Form.
     *
     * No report posts can be created: the form only has the mail handler enabled.
     */
    public function registerReportPostType(): void
    {
        $this->wpService->registerPostType('mun_a11y_report', [
            'label'               => $this->wpService->__('Accessibility issue reports', 'municipio'),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => false,
            'show_in_menu'        => false,
            'show_in_rest'        => false,
            'exclude_from_search' => true,
            'rewrite'             => false,
            'query_var'           => false,
            'supports'            => [],
        ]);
    }

    /**
     * Synchronize the recipient whenever the accessibility statement settings are saved.
     *
     * @param mixed $postId
     */
    public function syncReportFormModule(mixed $postId): void
    {
        if ($postId === 'options') {
            $this->ensureReportFormModule();
        }
    }

    /**
     * Sort known accessibility issues by category and then by label before ACF
     * persists the repeater rows.
     *
     * @param mixed $postId
     */
    public function sortKnownIssues(mixed $postId): void
    {
        if ($postId !== 'options') {
            return;
        }

        $repeaterFieldKey = 'field_68763f7edab51';
        $labelFieldKey    = 'field_68763ffc75441';
        $categoryFieldKey = 'field_6876400775442';
        $issues           = $_POST['acf'][$repeaterFieldKey] ?? null;

        if (!is_array($issues) || count($issues) < 2) {
            return;
        }

        usort($issues, function (mixed $first, mixed $second) use ($labelFieldKey, $categoryFieldKey): int {
            $first = is_array($first) ? $first : [];
            $second = is_array($second) ? $second : [];

            $categoryComparison = $this->compareKnownIssueCategories(
                $first[$categoryFieldKey] ?? '',
                $second[$categoryFieldKey] ?? '',
            );

            if ($categoryComparison !== 0) {
                return $categoryComparison;
            }

            return $this->compareKnownIssueLabels(
                (string) ($first[$labelFieldKey] ?? ''),
                (string) ($second[$labelFieldKey] ?? ''),
            );
        });

        $_POST['acf'][$repeaterFieldKey] = array_values($issues);
    }

    /**
     * Compare category values by the order in which they are offered in ACF.
     */
    private function compareKnownIssueCategories(mixed $first, mixed $second): int
    {
        $first = is_array($first) ? ($first['value'] ?? '') : (string) $first;
        $second = is_array($second) ? ($second['value'] ?? '') : (string) $second;

        $firstOrder = array_search($first, self::KNOWN_ISSUE_CATEGORY_ORDER, true);
        $secondOrder = array_search($second, self::KNOWN_ISSUE_CATEGORY_ORDER, true);
        $firstOrder = $firstOrder === false ? PHP_INT_MAX : $firstOrder;
        $secondOrder = $secondOrder === false ? PHP_INT_MAX : $secondOrder;

        return $firstOrder === $secondOrder
            ? strcasecmp($first, $second)
            : $firstOrder <=> $secondOrder;
    }

    /**
     * Compare labels according to the site's current locale when possible.
     */
    private function compareKnownIssueLabels(string $first, string $second): int
    {
        if (class_exists(\Collator::class)) {
            return (new \Collator(get_locale()))->compare($first, $second);
        }

        return strcasecmp($first, $second);
    }

    /**
     * Hide the programmatically managed form from the Frontend Form module list.
     */
    public function hideReportFormModuleFromAdmin(\WP_Query $query): void
    {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'mod-frontend-form') {
            return;
        }

        $metaQuery   = (array) $query->get('meta_query');
        $metaQuery[] = [
            'key'     => 'mun_a11ystatement_managed_form',
            'compare' => 'NOT EXISTS',
        ];
        $query->set('meta_query', $metaQuery);
    }

    /**
     * Explain why the report form is unavailable where its recipient is configured.
     */
    public function displayReportFormAdminNotice(): void
    {
        if (!is_admin() || ($_GET['page'] ?? '') !== 'a11ystatement') {
            return;
        }

        if (!class_exists('ModularityFrontendForm\\Module\\FrontendForm')) {
            $message = $this->wpService->__('The accessibility issue report form requires the Modularity Frontend Form plugin to be active.', 'municipio');
        } elseif ($this->getReportRecipientEmail() === null) {
            $message = $this->wpService->__('The accessibility issue report form is hidden until a valid recipient email address has been entered below.', 'municipio');
        } else {
            return;
        }

        echo '<div class="notice notice-warning"><p>' . esc_html($message) . '</p></div>';
    }

    /**
     * Allows the Frontend Form module to enqueue its assets for the virtual statement page.
     *
     * @param bool $hasModule
     * @param mixed $archiveSlug
     */
    public function loadReportFormAssets(bool $hasModule, mixed $archiveSlug): bool
    {
        return $hasModule || ($this->isFrontendFormAvailable()
            && (bool) $this->wpService->getQueryVar('a11y_statement'));
    }

    private function isFrontendFormAvailable(): bool
    {
        return class_exists('ModularityFrontendForm\\Module\\FrontendForm')
            && post_type_exists('mod-frontend-form');
    }

    private function getReportRecipientEmail(): ?string
    {
        $email = (string) $this->acfService->getField('mun_a11ystatement_report_recipient_email', 'options');

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
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
