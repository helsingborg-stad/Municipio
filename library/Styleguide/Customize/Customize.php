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

        $this->wpService->wpLocalizeScript(
            'styleguide-designbuilder-preview',
            'styleguide',
            [
                'translations' => [
                    'showUneditable' => $this->wpService->_x('Show uneditable', 'design-builder', 'municipio'),
                    'hideUneditable' => $this->wpService->_x('Hide uneditable', 'design-builder', 'municipio'),
                    'general' => $this->wpService->_x('General', 'design-builder', 'municipio'),
                    'components' => $this->wpService->_x('Components', 'design-builder', 'municipio'),
                    'chooseAPreset' => $this->wpService->_x('Choose a preset', 'design-builder', 'municipio'),
                    'pickOnPage' => $this->wpService->_x('Pick on page', 'design-builder', 'municipio'),
                    'stopPicking' => $this->wpService->_x('Stop picking', 'design-builder', 'municipio'),
                    'preset' => $this->wpService->_x('Preset', 'design-builder', 'municipio'),
                    'importExportJson' => $this->wpService->_x('Import/Export JSON', 'design-builder', 'municipio'),
                    'importJson' => $this->wpService->_x('Import JSON', 'design-builder', 'municipio'),
                    'exportJson' => $this->wpService->_x('Export JSON', 'design-builder', 'municipio'),
                    'resetActions' => $this->wpService->_x('Reset actions', 'design-builder', 'municipio'),
                    'resetAll' => $this->wpService->_x('Reset all', 'design-builder', 'municipio'),
                    'presetActions' => $this->wpService->_x('Preset actions', 'design-builder', 'municipio'),
                    'savePreset' => $this->wpService->_x('Save preset', 'design-builder', 'municipio'),
                    'deletePreset' => $this->wpService->_x('Delete preset', 'design-builder', 'municipio'),
                    'component' => $this->wpService->_x('Component', 'design-builder', 'municipio'),
                    'scope' => $this->wpService->_x('Scope', 'design-builder', 'municipio'),
                    'resetSelected' => $this->wpService->_x('Reset selected', 'design-builder', 'municipio'),
                    'savedPresets' => $this->wpService->_x('Saved presets', 'design-builder', 'municipio'),
                    'generalAllScopes' => $this->wpService->_x('General (all scopes)', 'design-builder', 'municipio'),
                ],
            ],
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
