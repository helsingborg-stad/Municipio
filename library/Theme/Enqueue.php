<?php

namespace Municipio\Theme;

use Municipio\Customizer\DesignLibrarySettingPolicy;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;
use WpUtilService\WpUtilService;

/**
 * Class Enqueue
 * @package Municipio\Theme
 */
class Enqueue implements Hookable
{
    private EnqueueManagerInterface $enqueue;
    private bool $hasLocalizedRestApiSettings = false;

    /**
     * Enqueue constructor.
     */
    public function __construct(
        private WpService $wpService,
        private WpUtilService $wpUtilService,
    ) {
        $this->enqueue = $this->wpUtilService->enqueue(__DIR__);
    }

    /**
     * Gets the REST API settings used by frontend scripts.
     */
    private function getRestApiSettings(): array
    {
        return [
            'root'                 => esc_url_raw(rest_url()),
            'nonce'                => wp_create_nonce('wp_rest'),
            'nonceRefreshCacheKey' => $this->getNonceRefreshCacheKey(),
            'versionString'        => 'wp/v2/'
        ];
    }

    /**
     * Gets a cache key namespace for the current authentication state.
     *
     * A nonce refresh URL must not be shared between logged-out visitors and
     * authenticated users. Roles provide a stable, non-sensitive namespace
     * without exposing a user ID or session value to the URL.
     */
    private function getNonceRefreshCacheKey(): string
    {
        if (!is_user_logged_in()) {
            return 'logged-out';
        }

        $roles = wp_get_current_user()->roles;

        if (!is_array($roles) || $roles === []) {
            return 'logged-in';
        }

        $roles = array_filter(
            array_map('sanitize_key', $roles),
            static fn ($role): bool => $role !== ''
        );
        sort($roles, SORT_STRING);

        return $roles === [] ? 'logged-in' : 'role-' . implode('-', $roles);
    }

    /**
     * Localizes REST API settings on the dependency shared by request scripts.
     */
    private function ensureRestApiSettings(): void
    {
        if ($this->hasLocalizedRestApiSettings) {
            return;
        }

        wp_localize_script('wp-api-fetch', 'wpApiSettings', $this->getRestApiSettings());
        $this->hasLocalizedRestApiSettings = true;
    }

    /**
     * Add hooks
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'enqueueFrontendScriptsAndStyles'], 5);
        $this->wpService->addAction('admin_enqueue_scripts', [$this, 'enqueueAdminScriptsAndStyles'], 999);
        $this->wpService->addAction(
            'customize_controls_enqueue_scripts',
            [$this, 'enqueueCustomizerScriptsAndStyles'],
            999,
        );
        $this->wpService->addAction(
            'customize_preview_init',
            [$this, 'enqueueCustomizerPreviewScripts'],
            999,
        );
        $this->wpService->addAction('wp_default_scripts', [$this, 'removeJqueryMigrate']);
        $this->wpService->addFilter('gform_init_scripts_footer', [$this, 'forceGravityFormsScriptsNotInFooter']);
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueueFrontendScriptsAndStyles()
    {
        //Add municipio.js with translations
        $this->enqueue
            ->add('js/municipio.js', ['wp-api-fetch'])
            ->with()
            ->translation('MunicipioLocale', [
                'printbreak' => ['tooltip' => $this->wpService->__('Insert Print Page Break tag', 'municipio')],
                'messages' => [
                    'deleteComment' => $this->wpService->__('Are you sure you want to delete the comment?', 'municipio'),
                    'onError' => $this->wpService->__('Something went wrong, please try again later', 'municipio'),
                ],
                'a11yWarnings' => [
                    'button' => $this->wpService->__('Button text is not descriptive enough', 'municipio'),
                    'headingHierarchy' => $this->wpService->__('Invalid heading level (level is skipped)', 'municipio'),
                    'link' => $this->wpService->__('Link text is not descriptive enough', 'municipio'),
                    'vagueLabels' => [
                        $this->wpService->__('Click here', 'municipio'),
                        $this->wpService->__('Here', 'municipio'),
                        $this->wpService->__('Read more', 'municipio'),
                        $this->wpService->__('Continue reading', 'municipio'),
                        $this->wpService->__('More', 'municipio'),
                    ],
                ],
            ]);
        $this->ensureRestApiSettings();

        //Add styleguide.js with translations
        $this->enqueue
            ->add('js/styleguide.js')
            ->with()
            ->translation('localizedMonths', [
                ucFirst($this->wpService->__('January', 'municipio')),
                ucFirst($this->wpService->__('February', 'municipio')),
                ucFirst($this->wpService->__('March', 'municipio')),
                ucFirst($this->wpService->__('April', 'municipio')),
                ucFirst($this->wpService->__('May', 'municipio')),
                ucFirst($this->wpService->__('June', 'municipio')),
                ucFirst($this->wpService->__('July', 'municipio')),
                ucFirst($this->wpService->__('August', 'municipio')),
                ucFirst($this->wpService->__('September', 'municipio')),
                ucFirst($this->wpService->__('October', 'municipio')),
                ucFirst($this->wpService->__('November', 'municipio')),
                ucFirst($this->wpService->__('December', 'municipio')),
            ])
            ->and()
            ->translation('localizedDays', [
                ucFirst($this->wpService->__('Su', 'municipio')),
                ucFirst($this->wpService->__('Mo', 'municipio')),
                ucFirst($this->wpService->__('Tu', 'municipio')),
                ucFirst($this->wpService->__('We', 'municipio')),
                ucFirst($this->wpService->__('Th', 'municipio')),
                ucFirst($this->wpService->__('Fr', 'municipio')),
                ucFirst($this->wpService->__('Sa', 'municipio')),
            ]);

        //Other scripts
        $this->enqueue->add('js/instantpage.js');
        $this->enqueue->add('js/pdf.js');
        $this->enqueue->add('js/nav.js');

        //Other styles
        $this->enqueue->add('css/municipio.css');
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueueAdminScriptsAndStyles()
    {
        $this->enqueue->add('js/user-group-visibility.js');
        $this->enqueue->add('js/hidden-post-status-conditional.js', ['acf-input', 'jquery']);
        $this->enqueue->add('js/admin-progress-action.js');

        $this->enqueue->add('css/acf.css');
        $this->enqueue->add('css/general.css');
        $this->enqueue->add('css/a11y.css');
        $this->enqueue->add('css/trash-page.css');
    }

    /**
     * Enqueue customizer scripts and styles
     */
    public function enqueueCustomizerScriptsAndStyles()
    {
        $this->enqueue
            ->add('js/design-share.js', ['jquery', 'customize-controls', 'wp-api-fetch'])
            ->with()
            ->translation('municipioDesignShareConfig', [
                'minimumSupportedDbVersion' => (int) get_option('municipio_db_version', 0),
                'allowedSettingKeys' => DesignLibrarySettingPolicy::getAllowedExactKeys(),
                'allowedSettingKeyPrefixes' => DesignLibrarySettingPolicy::getAllowedPrefixes(),
            ]);
        $this->ensureRestApiSettings();

        $this->enqueue->add('js/customizer-error-handling.js', ['jquery', 'customize-controls']);
        $this->enqueue->add('js/customizer-uploaded-font-labels.js', ['jquery', 'customize-controls']);
    }

    /**
     * Enqueue customizer preview scripts.
     */
    public function enqueueCustomizerPreviewScripts(): void
    {
        $this->enqueue->add('js/customizer-header-logo-scroll-aspect-ratio-preview.js', ['customize-preview']);
    }

    /**
     * Removes generator tag
     */
    public function removeGeneratorTag($a, $b): string
    {
        return '';
    }

    /**
     * Move all scripts to footer, discard settings.
     *
     * @return void
     */
    public function moveScriptsToFooter(): void
    {
        global $wp_scripts;
        $notInFooter = array_diff($wp_scripts->queue, $wp_scripts->in_footer);
        $wp_scripts->in_footer = array_merge($wp_scripts->in_footer, $notInFooter);
    }

    /**
     * Remove jquery migrate from default scripts
     */
    public function removeJqueryMigrate(mixed $scripts): void
    {
        if ($this->wpService->isAdmin()) {
            return;
        }
        if (!empty($scripts->registered['jquery'])) {
            $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
        }
    }

    /**
     * Do not load Gravity Forms scripts in the footer unless you want to work the weekend
     */
    public function forceGravityFormsScriptsNotInFooter(): bool
    {
        return false;
    }
}
