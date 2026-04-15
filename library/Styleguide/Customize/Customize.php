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
        $this->wpService->addFilter('Municipio/Styleguide/CustomizeMarkup', [$this, 'getCustomizeMarkup']);
    }

    public function registerThemeMod(\WP_Customize_Manager $wpCustomize): void
    {
        $wpCustomize->add_setting('tokens', [
            'default' => '{"token": {}, "component": {}}',
            'transport' => 'postMessage',
        ]);
        $wpCustomize->add_control('tokens', [
            'id' => 'tokens',
            'type' => 'hidden',
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

    public function getCustomizeMarkup(): ?string
    {
        if (!$this->wpService->isCustomizePreview()) {
            return null;
        }

        $overrideStateService = new \Municipio\Styleguide\Customize\OverrideState\OverrideState($this->wpService);

        $overrideState = json_encode($overrideStateService->getOverrideState());
        $tokenData = json_encode((new \Municipio\Styleguide\Customize\TokenData\TokenData($this->wpService, $overrideStateService))->getTokenData());
        $componentData = json_encode((new \Municipio\Styleguide\Customize\ComponentData\ComponentData($this->wpService))->getComponentData());

        $componentData = htmlspecialchars($componentData, ENT_QUOTES, 'UTF-8');
        $tokenData = htmlspecialchars($tokenData, ENT_QUOTES, 'UTF-8');
        $overrideState = htmlspecialchars($overrideState, ENT_QUOTES, 'UTF-8');

        return "<design-builder component-data='" . $componentData . "' token-data='" . $tokenData . "' override-state='" . $overrideState . "' show-save-button='false'></design-builder>";
    }
}
