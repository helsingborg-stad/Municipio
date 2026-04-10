<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize;

use Composer\InstalledVersions;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Styleguide\Customize\RestApi\Support\CustomizeTokensReaderInterface as SupportCustomizeTokensReaderInterface;
use WpService\WpService;

class StyleguideDesignBuilder implements Hookable
{
    private const STYLEGUIDE_PACKAGE = 'helsingborg-stad/styleguide';

    private ?string $styleguidePath = null;

    public function __construct(
        private readonly WpService $wpService,
        private readonly SupportCustomizeTokensReaderInterface $tokensReader,
    ) {
        $this->styleguidePath = InstalledVersions::getInstallPath(
            self::STYLEGUIDE_PACKAGE,
        ) ?: null;
    }

    /* Registers the necessary hooks for adding customizer properties to components.
     *
     * This method checks if the editor should be enabled based on the current user's capabilities.
     * If the editor is enabled, it adds an action to modify the component data with customizer properties.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'outputDesignBuilderStyles']);
        $this->wpService->addAction('init', [$this, 'enqueueCustomizeAssets']);
        $this->wpService->addFilter('Municipio/Styleguide/DesignBuilderMarkup', [$this, 'getDesignBuilderMarkup']);
    }

    /* Checks if the editor should be enabled based on the current user's capabilities.
     *
     * @return bool True if the editor should be enabled, false otherwise.
     */
    private function shouldEnableEditor(): bool
    {
        if ($this->wpService->isCustomizePreview() && $this->wpService->currentUserCan('edit_theme_options')) {
            return true;
        }

        return false;
    }

    public function outputDesignBuilderStyles(): void
    {
        if ($this->shouldEnableEditor() === true) {
            return;
        }

        $this->wpService->wpRegisterStyle('styleguide-design-builder-output', false);
        $this->wpService->wpEnqueueStyle('styleguide-design-builder-output');
        $this->wpService->wpAddInlineStyle('styleguide-design-builder-output', $this->getTokensAsCss());
    }

    private function getTokensAsCss(): string
    {
        $default = json_encode(['design' => ['token' => [], 'component' => []]]);
        $stored = get_theme_mod('tokens', $default);
        $stored = json_decode($stored, true);
        $generalTokens = $stored['design']['token'];
        $componentTokens = $stored['design']['component'];

        $designTokensCssConverter = new DesignTokensToCssConverter\DesignTokensToCssConverter();
        $css = $designTokensCssConverter->convert(array_merge($generalTokens, $componentTokens));
        return '@layer theme {' . $css . '}';
    }

    /* Enqueues the necessary assets for the customizer editor.
     *
     * This method registers and enqueues the stylesheet for the customizer editor, ensuring that it is loaded on the appropriate pages.
     */
    public function enqueueCustomizeAssets(): void
    {
        if (!$this->shouldEnableEditor()) {
            return;
        }

        $this->wpService->wpEnqueueStyle(
            'styleguide-customize',
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('css/designbuilder.css'),
        );

        $this->wpService->wpEnqueueScript(
            'styleguide-customize',
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/designbuilder.js'),
        );

        $this->wpService->wpEnqueueScript(
            'municipio-customize',
            $this->wpService->getTemplateDirectoryUri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/customize.js'),
        );
    }

    public function getDesignBuilderMarkup(): ?string
    {
        if ($this->shouldEnableEditor() === false) {
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
            'token' => $stored['design']['token'],
            'component' => $stored['design']['component'],
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

        return file_get_contents($filePath) ?: null;
    }

    private function getTokenData(): ?string
    {
        $filePath = realpath($this->styleguidePath) . '/source/data/design-tokens.json';
        if (!file_exists($filePath)) {
            return null;
        }

        return file_get_contents($filePath) ?: null;
    }
}
