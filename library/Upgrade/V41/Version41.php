<?php

namespace Municipio\Upgrade\V41;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use Municipio\Customizer\Applicators\Types\NullApplicator;
use Municipio\Helper\AcfService;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetThemeMods;
use WpService\Contracts\UpdateOption;
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
        private WpGetCustomCss&WpUpdateCustomCssPost&GetOption&UpdateOption $wpService,
        private GetField&UpdateField $acfService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $this->migrateCustomCss();
        $this->migrateThemeMods();
    }

    private function migrateCustomCss(): void
    {
        $customCss = $this->wpService->wpGetCustomCss();
        $acfCustomCss = $this->acfService->getField('custom_css_input', 'option');

        $searchReplaceMap = [
            '--color-header-background' => '--c-header--background-color',
            '--color-primary' => '--color--primary',
            '--color-secondary' => '--color--secondary',
            '--color-breadcrumb-icon' => '--c-breadcrumb--color--background-contrast-muted',
            '--color-background' => '--color--background',
            '--c-header-logotype-height' => '--c-header--logotype-height',
            '--c-header-brand-color' => '--c-header--brand-color',
            '--color--primary-light' => '--color--primary',
            'height: calc(var(--c-header--logotype-height, 6) * var(--base, 8px));' => 'height: var(--c-header--logotype-height);',
            'width: calc(var(--c-header--logotype-height, 6) * var(--base, 8px));' => 'width: var(--c-header--logotype-height);',
        ];

        $customCss = str_replace(array_keys($searchReplaceMap), array_values($searchReplaceMap), $customCss);
        $acfCustomCss = str_replace(array_keys($searchReplaceMap), array_values($searchReplaceMap), $acfCustomCss);

        $customCss = $this->maybeWrapInCssLayer($customCss);
        $acfCustomCss = $this->maybeWrapInCssLayer($acfCustomCss);

        $this->wpService->wpUpdateCustomCssPost($customCss);
        $this->acfService->updateField('custom_css_input', $acfCustomCss, 'option');
    }

    private function migrateThemeMods(): void
    {
        $themeMods = $this->wpService->getOption('theme_mods_municipio', []);

        if (isset($themeMods['quicklinks_appearance_type']) && $themeMods['quicklinks_appearance_type'] === 'custom') {
            $themeMods['quicklinks_color_scheme'] = 'secondary';
        }

        unset($themeMods['quicklinks_appearance_type']);
        unset($themeMods['quicklinks_background_type']);
        unset($themeMods['quicklinks_custom_background']);

        $this->wpService->updateOption('theme_mods_municipio', $themeMods);
    }

    private function maybeWrapInCssLayer(string $css): string
    {
        if (str_contains($css, '@layer')) {
            return $css;
        }

        return "@layer theme {\n" . $css . "\n}";
    }
}
