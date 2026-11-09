<?php

namespace Municipio\PrivacyPolicy;

use AcfService\AcfService;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\AddRewriteRule;
use WpService\Contracts\FlushRewriteRules;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetQueryVar;
use WpService\Contracts\HomeUrl;

/**
 * Privacy policy (personal data policy) managed from an options page.
 *
 * The field group can be shared from the main site to all subsites through
 * the common field groups feature, just like the accessibility statement.
 */
class PrivacyPolicy implements Hookable
{
    public const OPTIONS_PAGE_SLUG = 'privacypolicy';
    public const QUERY_VAR         = 'privacy_policy';
    public const TEMPLATE          = 'privacy-policy';

    public function __construct(
        private AddAction&AddFilter&AddRewriteRule&FlushRewriteRules&GetOption&GetQueryVar&HomeUrl&__ $wpService,
        private AcfService $acfService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'registerOptionsPage']);
        // Common field groups load inherited values at init priority 15. The
        // policy route must be registered afterwards on sites that inherit
        // the privacy policy settings.
        $this->wpService->addAction('init', [$this, 'registerFrontendPage'], 20);
        $this->wpService->addFilter('acf/load_field/key=field_mun_privacypolicy_url_message', [$this, 'loadPrivacyPolicyUrlField']);
    }

    /**
     * Register the options page for the privacy policy.
     */
    public function registerOptionsPage(): void
    {
        $this->acfService->addOptionsPage([
            'page_title'      => $this->wpService->__('Privacy Policy', 'municipio'),
            'menu_title'      => $this->wpService->__('Privacy Policy', 'municipio'),
            'menu_slug'       => self::OPTIONS_PAGE_SLUG,
            'capability'      => 'manage_options',
            'redirect'        => true,
            'update_button'   => $this->wpService->__('Update', 'municipio'),
            'updated_message' => $this->wpService->__('The privacy policy has been updated.', 'municipio'),
            'parent_slug'     => 'options-general.php',
        ]);
    }

    /**
     * Replace the url placeholder in the "View privacy policy" field.
     */
    public function loadPrivacyPolicyUrlField(array $field): array
    {
        $field['message'] = str_replace(
            '{{privacy_policy_page_url}}',
            $this->getFullPolicyUrl(),
            (string) ($field['message'] ?? '')
        );

        return $field;
    }

    /**
     * Whether the privacy policy page is enabled.
     */
    public function isEnabled(): bool
    {
        return (bool) $this->acfService->getField('mun_privacypolicy_enabled', 'options');
    }

    /**
     * Register the virtual frontend page for the privacy policy.
     */
    public function registerFrontendPage(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $slug = $this->getFrontendPageSlug();

        $this->wpService->addRewriteRule(
            '^' . $slug . '/?$',
            'index.php?' . self::QUERY_VAR . '=1',
            'top'
        );

        $this->wpService->addFilter('query_vars', function ($vars) {
            $vars[] = self::QUERY_VAR;
            return $vars;
        });

        $this->wpService->addFilter('template_include', function ($template) {
            if ($this->wpService->getQueryVar(self::QUERY_VAR)) {
                return self::TEMPLATE;
            }
            return $template;
        });

        // Auto-flush rewrite rules if our custom rule isn't present
        $this->wpService->addAction('wp_loaded', function () use ($slug) {
            $rules = $this->wpService->getOption('rewrite_rules');
            if (!isset($rules['^' . $slug . '/?$'])) {
                $this->wpService->flushRewriteRules();
            }
        });

        $this->wpService->addFilter('Municipio/Template/MayBeCustomTemplateRequest', function ($mayBeCustomTemplateRequest) {
            if ($this->wpService->getQueryVar(self::QUERY_VAR)) {
                return true;
            }
            return $mayBeCustomTemplateRequest;
        });

        // Let WordPress core (e.g. the_privacy_policy_link()) point to this page.
        $this->wpService->addFilter('privacy_policy_url', fn () => $this->getFullPolicyUrl());
    }

    /**
     * Get the slug for the frontend privacy policy page.
     */
    private function getFrontendPageSlug(): string
    {
        return $this->wpService->__('privacy-policy', 'municipio');
    }

    /**
     * Get the full URL for the privacy policy.
     */
    private function getFullPolicyUrl(): string
    {
        return $this->wpService->homeUrl($this->getFrontendPageSlug());
    }
}
