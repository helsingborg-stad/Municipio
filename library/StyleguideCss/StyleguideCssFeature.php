<?php

namespace Municipio\StyleguideCss;

use Municipio\HooksRegistrar\Hookable;
use Municipio\StyleguideCss\CssVariables\CssVariablesRenderer;
use Municipio\StyleguideCss\CssVariables\CssVariablesRendererInterface;
use Municipio\StyleguideCss\ThemeSettingsMapper\ThemeSettingsMapper;
use Municipio\StyleguideCss\ThemeSettingsMapper\ThemeSettingsMapperInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetThemeMods;

class StyleguideCssFeature implements Hookable
{
    public function __construct(
        private AddAction&GetThemeMods $wpService,
        private ThemeSettingsMapperInterface $themeSettingsMapper = new ThemeSettingsMapper(),
        private CssVariablesRendererInterface $cssVariablesRenderer = new CssVariablesRenderer(),
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_head', [$this, 'outputStyleguideCss']);
    }

    public function outputStyleguideCss(): void
    {
        $cssVariablesCollection = $this->themeSettingsMapper->map($this->wpService->getThemeMods());
        $css = $this->cssVariablesRenderer->render($cssVariablesCollection);
        echo "<style>\n@layer theme {\n{$css}}\n</style>\n";
    }
}
