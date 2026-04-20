<?php

namespace Municipio\Upgrade\V41;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetOption;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

/**
 * Class Version41
 */
class Version41 implements VersionInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private WpGetCustomCss&WpUpdateCustomCssPost&GetOption&SetThemeMod $wpService,
        private GetField&UpdateField $acfService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $this->migrateCustomizerSettingsToDesignTokens();
        $this->migrateCustomCss();
    }

    private function migrateCustomizerSettingsToDesignTokens(): void
    {
        $themeMods = $this->wpService->getOption('theme_mods_municipio', []);
        // echo '<pre>' . print_r($themeMods, true) . '</pre>';
        // die();
        $tokens = (new MapThemeModsToDesignTokens())->map($themeMods);
        $tokens = (new DecorateDesignTokens())->decorate($tokens);

        $this->wpService->setThemeMod('tokens', json_encode($tokens));
    }

    private function migrateCustomCss(): void
    {
        $customCss = $this->wpService->wpGetCustomCss();
        $acfCustomCss = $this->acfService->getField('custom_css_input', 'option');

        $customCss = (new MigrateCustomCss())->migrate($customCss);
        $acfCustomCss = (new MigrateCustomCss())->migrate($acfCustomCss);

        $this->wpService->wpUpdateCustomCssPost($customCss);
        $this->acfService->updateField('custom_css_input', $acfCustomCss, 'option');
    }
}
