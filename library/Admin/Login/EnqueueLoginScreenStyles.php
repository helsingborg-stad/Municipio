<?php

namespace Municipio\Admin\Login;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Helper\CacheBust;
use WpService\WpService;

class EnqueueLoginScreenStyles implements Hookable
{
    private const ASSETS_DIST_PATH = '/assets/dist/';

    public function __construct(private WpService $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('login_head', [$this, 'renderCssVariables']);
        $this->wpService->addAction('login_head', [$this, 'renderKirkiCssVariables']);
        $this->wpService->addAction('login_enqueue_scripts', [$this, 'enqueueStyles']);
    }

    /**
     * Enqueue styles for the login page.
     *
     * @return void
     */
    public function enqueueStyles(): void
    {
        $styles = [
            'styleguide-css'      => 'css/styleguide.css',
            'municipio-css'       => 'css/municipio.css',
            'municipio-login-css' => 'css/login.css',
        ];

        foreach ($styles as $handle => $path) {
            wp_register_style($handle, $this->getAssetWithCacheBust($path));
            wp_enqueue_style($handle);
        }
    }

    /**
     * Render CSS variables for the login page.
     *
     * @return void
     */
    public function renderCssVariables(): void
    {
        // Define theme mods to be used as CSS variables
        $themeMods = [
            'logotype' => $this->getLogotype(),
        ];
        $themeMods = array_filter($themeMods);

        // Reduce theme mods to CSS variables
        $reduced = implode(' ', array_map(function ($value, $key) {
            return "--" . esc_html($key) . ": " . esc_html($value) . ";";
        }, $themeMods, array_keys($themeMods)));

        // Output CSS variables
        echo sprintf('<style>:root {%s}</style>', $reduced);
    }

    /**
     * Render Kirki CSS variables for the login page.
     *
     * @return void
     */
    public function renderKirkiCssVariables(): void
    {
        echo '<style>' . PHP_EOL;
            do_action('kirki_dynamic_css');
        echo '</style>' . PHP_EOL;
    }

    /**
     * Get logotype URL.
     */
    private function getLogotype(): string
    {
        $logotype = get_theme_mod('logotype');
        if (empty($logotype)) {
            return get_stylesheet_directory_uri() . '/assets/images/municipio.svg';
        }
        return 'url(' . $logotype . ')';
    }

    /**
     * Get cache-busted asset file url.
     */
    private function getAssetWithCacheBust(string $file): string
    {
        return implode([
            get_template_directory_uri(),
            self::ASSETS_DIST_PATH,
            \Municipio\Helper\CacheBust::name($file)
        ]);
    }
}
