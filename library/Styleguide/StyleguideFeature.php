<?php

namespace Municipio\Styleguide;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Styleguide\AddLayerOrderDefinitionToHead\AddLayerOrderDefinitionToHead;
use Municipio\Styleguide\ApplyLayerToInlineStyles\ApplyLayerToInlineStyles;
use Municipio\Styleguide\ApplyLayerToWordpressStyles\ApplyLayerToWordpressStyles;
use Municipio\Styleguide\CssVariables\CssVariablesEditorRenderer;
use Municipio\Styleguide\CssVariables\CssVariablesRenderer;
use Municipio\Styleguide\CssVariables\CssVariablesRendererInterface;
use Municipio\Styleguide\Customize\StyleguideDesignBuilder;
use Municipio\Styleguide\ThemeSettingsMapper\ThemeSettingsMapper;
use Municipio\Styleguide\ThemeSettingsMapper\ThemeSettingsMapperInterface;
use WpService\WpService;

class StyleguideFeature implements Hookable
{
    public function __construct(
        private WpService $wpService,
        private ThemeSettingsMapperInterface $themeSettingsMapper = new ThemeSettingsMapper(),
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', fn() => $this->outputStyleguideCss());
        $this->wpService->addAction('login_enqueue_scripts', fn() => $this->outputStyleguideCss());
        $this->wpService->addAction('enqueue_block_editor_assets', fn() => $this->outputStyleguideCss(true));
        (new ApplyLayerToInlineStyles($this->wpService))->addHooks();
        (new AddLayerOrderDefinitionToHead($this->wpService))->addHooks();
        (new ApplyLayerToWordpressStyles($this->wpService))->addHooks();

        $this->setupCustomizer();
    }

    public function outputStyleguideCss(bool $isEditor = false): void
    {
        $cssVariables = $this->themeSettingsMapper->map($this->wpService->getThemeMods());
        $cssVariables = $this->applyCssVariableFilters($cssVariables);
        $css = $this->getCssVariablesRenderer($isEditor)->render(...$cssVariables);
        $css = "@layer theme {{$css}}";

        $this->enqueueStyles($css, $isEditor);
    }

    private function enqueueStyles(string $css, bool $isEditor = false): void
    {
        if ($isEditor) {
            $this->wpService->wpRegisterStyle('styleguide-css-variables', false);
            $this->wpService->wpEnqueueStyle('styleguide-css-variables');
            $this->wpService->wpAddInlineStyle('styleguide-css-variables', $css);
            add_editor_style($this->getStyleguideTemplatePath());
        } else {
            $this->wpService->wpEnqueueStyle('styleguide-css-variables', get_template_directory_uri() . $this->getStyleguideTemplatePath());
            $this->wpService->wpAddInlineStyle('styleguide-css-variables', $css);
        }
    }

    private function getStyleguideTemplatePath(): string
    {
        return '/assets/dist/' . \Municipio\Helper\CacheBust::name('css/styleguide.css');
    }

    private function setupCustomizer(): void
    {
        $tokensConfig = new Customize\RestApi\Config\CustomizeConfig($this->wpService);
        $tokensReader = new Customize\RestApi\Support\CustomizeTokensReader($this->wpService, $tokensConfig, new Customize\RestApi\Support\ChangesetIdResolver($this->wpService));
        $tokensWriter = new Customize\RestApi\Support\CustomizeTokensWriter($this->wpService, $tokensConfig, new Customize\RestApi\Support\ChangesetIdResolver($this->wpService));

        (new StyleguideDesignBuilder($this->wpService, $tokensReader))->addHooks();
        (new Customize\RestApi\Get($this->wpService, $tokensConfig, $tokensReader))->register();
        (new Customize\RestApi\Save($this->wpService, $tokensConfig, $tokensWriter))->register();

        $tokensConfig = new Customize\RestApi\Config\CustomizeConfig($this->wpService);
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
