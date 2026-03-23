<?php

namespace Municipio\StyleguideCss;

use Municipio\HooksRegistrar\Hookable;
use Municipio\StyleguideCss\ApplyLayerToWordpressStyles\ApplyLayerToWordpressStyles;
use Municipio\StyleguideCss\CssVariables\CssVariablesEditorRenderer;
use Municipio\StyleguideCss\CssVariables\CssVariablesRenderer;
use Municipio\StyleguideCss\CssVariables\CssVariablesRendererInterface;
use Municipio\StyleguideCss\ThemeSettingsMapper\ThemeSettingsMapper;
use Municipio\StyleguideCss\ThemeSettingsMapper\ThemeSettingsMapperInterface;
use WpService\WpService;

class StyleguideCssFeature implements Hookable
{
    public function __construct(
        private WpService $wpService,
        private ThemeSettingsMapperInterface $themeSettingsMapper = new ThemeSettingsMapper()
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', fn() => $this->outputStyleguideCss());
        $this->wpService->addAction('login_enqueue_scripts', fn() => $this->outputStyleguideCss());
        $this->wpService->addAction('enqueue_block_editor_assets', fn() => $this->outputStyleguideCss(true));
        (new ApplyLayerToWordpressStyles($this->wpService))->addHooks();
    }

    public function outputStyleguideCss(bool $isEditor = false): void
    {
        $cssVariables = $this->themeSettingsMapper->map($this->wpService->getThemeMods());
        $cssVariables = $this->applyCssVariableFilters($cssVariables);
        $css = $this->getCssVariablesRenderer($isEditor)->render(...$cssVariables);
        /*$this->enqueueStyles("@layer theme {{$css}}");*/
        $this->enqueueStyles($css);
    }

    private function enqueueStyles(string $css, bool $isEditor = false):void {
        $this->wpService->wpRegisterStyle('styleguide-css-variables', false);
        $this->wpService->wpEnqueueStyle('styleguide-css-variables');
        $this->wpService->wpAddInlineStyle('styleguide-css-variables', $css);
        if( $isEditor ) {
            add_editor_style($this->getStyleguideTemplatePath());
        } else {
            $this->wpService->wpAddInlineStyle('styleguide-css-variables', $this->getStyleguideCssContent());
        }
    }

    private function getStyleguideCssContent():string {
        $css = file_get_contents($this->wpService->getTemplateDirectory() . $this->getStyleguideTemplatePath());
        return ":root :where(.editor-styles-wrapper) {\n$css\n})";
    }

    private function getStyleguideTemplatePath(): string {
        return '/assets/dist/' . \Municipio\Helper\CacheBust::name('css/styleguide.css');
    }

    private function applyCssVariableFilters(array $cssVariables): array
    {
        $filters = [
            new CssVariables\CssVariablesFilters\TranslateLegacyFieldBorderRadius(),
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

    private function getCssVariablesRenderer(bool $isEditor = false): CssVariablesRendererInterface
    {
        return $isEditor ? new CssVariablesEditorRenderer() : new CssVariablesRenderer();
    }
}
