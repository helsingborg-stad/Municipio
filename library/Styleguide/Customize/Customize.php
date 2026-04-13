<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize;

use Composer\InstalledVersions;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class Customize implements Hookable
{
    private const STYLEGUIDE_PACKAGE = 'helsingborg-stad/styleguide';

    private ?string $styleguidePath = null;

    public function __construct(
        private readonly WpService $wpService,
    ) {
        $this->styleguidePath =
            InstalledVersions::getInstallPath(
                self::STYLEGUIDE_PACKAGE,
            ) ?? null;
    }

    /* Registers the necessary hooks for adding customizer properties to components.
     *
     * This method checks if the editor should be enabled based on the current user's capabilities.
     * If the editor is enabled, it adds an action to modify the component data with customizer properties.
     */
    public function addHooks(): void
    {
        (new ApplyStyles\ApplyStyles($this->wpService))->addHooks();
        $this->wpService->addAction('customize_register', [$this, 'registerThemeMod']);
        $this->wpService->addAction('customize_controls_enqueue_scripts', [$this, 'enqueueControlsAssets']);
        $this->wpService->addAction('customize_preview_init', [$this, 'enqueuePreviewAssets']);
        $this->wpService->addFilter('Municipio/Styleguide/DesignBuilderMarkup', [$this, 'getDesignBuilderMarkup']);
    }

    public function registerThemeMod(\WP_Customize_Manager $wpCustomize): void
    {
        $wpCustomize->add_setting('tokens', [
            'default' => '{design: {token: {}, component: {}}}',
            'transport' => 'postMessage',
        ]);
        $wpCustomize->add_control('tokens', [
            'id' => 'tokens',
            'type' => 'hidden',
            'value' => '{design: {token: {}, component: {}}}',
            'section' => 'title_tagline',
        ]);
    }

    /* Enqueues the necessary assets for the customizer editor.
     *
     * This method registers and enqueues the stylesheet for the customizer editor, ensuring that it is loaded on the appropriate pages.
     */
    public function enqueueControlsAssets(): void
    {
        $this->wpService->wpEnqueueScript(
            'municipio-customize',
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/customize.js'),
            ['customize-controls'],
            false,
            ['in_footer' => true],
        );
    }

    public function enqueuePreviewAssets(): void
    {
        if (!$this->wpService->isCustomizePreview()) {
            return;
        }

        $this->wpService->wpEnqueueStyle(
            'styleguide-designbuilder',
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('css/designbuilder.css'),
        );

        $this->wpService->wpRegisterScript(
            'styleguide-designbuilder',
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/designbuilder.js'),
        );

        $this->wpService->wpEnqueueScript(
            'styleguide-designbuilder-preview',
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/designbuilder-preview.js'),
            ['customize-preview', 'styleguide-designbuilder'],
        );
    }

    public function getDesignBuilderMarkup(): ?string
    {
        if (!$this->wpService->isCustomizePreview()) {
            return null;
        }

        $componentData = $this->getComponentData();
        $tokenLibrary = $this->getTokenData();

        $componentData = htmlspecialchars($componentData, ENT_QUOTES, 'UTF-8');
        $tokenLibrary = htmlspecialchars($tokenLibrary, ENT_QUOTES, 'UTF-8');

        $default = json_encode(['design' => ['token' => [], 'component' => []]]);
        $stored = get_theme_mod('tokens', $default);
        $stored = json_decode($stored, true);
        $overrideState = [
            'token' => $stored['token'],
            'component' => $stored['component'],
        ];

        $overrideState = htmlspecialchars(json_encode($overrideState), ENT_QUOTES, 'UTF-8');

        return "<design-builder component-data='" . $componentData . "' token-data='" . $tokenLibrary . "' token-library='" . $tokenLibrary . "' override-state='" . $overrideState . "'></design-builder>";
    }

    /* Reads the customizer data from a specified file and returns its contents.
     *
     * This method checks if the file exists and is readable, then returns its contents as a string. If the file does not exist or cannot be read, it returns null.
     *
     * @return string|null The contents of the customizer data file, or null if the file cannot be read.
     */
    private function getComponentData(): ?string
    {
        $filePath = realpath($this->styleguidePath) . '/component-design-tokens.json';
        if (!file_exists($filePath)) {
            return null;
        }

        $contents = file_get_contents($filePath);
        return $contents === false ? null : $contents;
    }

    private function getTokenData(): ?string
    {
        $filePath = realpath($this->styleguidePath) . '/source/data/design-tokens.json';
        if (!file_exists($filePath)) {
            return null;
        }

        $contents = file_get_contents($filePath);
        return $contents === false ? null : $contents;
    }
}
