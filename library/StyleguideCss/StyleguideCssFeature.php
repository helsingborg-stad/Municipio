<?php

namespace Municipio\StyleguideCss;

use Municipio\HooksRegistrar\Hookable;
use Municipio\StyleguideCss\ApplyLayerToWordpressStyles\ApplyLayerToWordpressStyles;
use Municipio\StyleguideCss\CssVariables\CssVariablesRenderer;
use Municipio\StyleguideCss\CssVariables\CssVariablesRendererInterface;
use Municipio\StyleguideCss\ThemeSettingsMapper\ThemeSettingsMapper;
use Municipio\StyleguideCss\ThemeSettingsMapper\ThemeSettingsMapperInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetThemeMods;

class StyleguideCssFeature implements Hookable
{
    public function __construct(
        private AddAction&GetThemeMods&AddFilter $wpService,
        private ThemeSettingsMapperInterface $themeSettingsMapper = new ThemeSettingsMapper(),
        private CssVariablesRendererInterface $cssVariablesRenderer = new CssVariablesRenderer(),
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_head', [$this, 'outputStyleguideCss']);
        $this->wpService->addAction('login_head', [$this, 'outputStyleguideCss']);
        (new ApplyLayerToWordpressStyles($this->wpService))->addHooks();
    }

    public function outputStyleguideCss(): void
    {
        $cssVariables = $this->themeSettingsMapper->map($this->wpService->getThemeMods());
        $cssVariables = $this->applyCssVariableFilters($cssVariables);
        $css = $this->cssVariablesRenderer->render(...$cssVariables);
        echo "<style>\n@layer theme {{$css}}</style>\n";
    }

    private function applyCssVariableFilters(array $cssVariables): array
    {
        $filters = [
            new CssVariables\CssVariablesFilters\TranslateLegacyHeaderLogotypeHeight(),
            new CssVariables\CssVariablesFilters\TranslateLegacyBorderWidth(),
            new CssVariables\CssVariablesFilters\TranslateLegacyBorderRadius(),
            new CssVariables\CssVariablesFilters\TranslateLegacyContainerWidth(),
            new CssVariables\CssVariablesFilters\TranslateLegacyFooterLogotypeHeight(),
        ];

        foreach ($filters as $filter) {
            $cssVariables = array_map($filter->apply(...), $cssVariables);
        }

        return $cssVariables;
    }
}
