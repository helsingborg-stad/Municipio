<?php

namespace Municipio\BrandedEmails\HtmlTemplate\Config;

use WpService\Contracts\GetStylesheetDirectoryUri;
use WpService\Contracts\GetThemeMod;

class HtmlTemplateConfigService implements HtmlTemplateConfig
{
    public function __construct(private GetThemeMod&GetStylesheetDirectoryUri $wpService)
    {
    }

    public function getBackgroundColor(): string
    {
        return $this->wpService->getThemeMod('color_background')['background'] ?? '#FFFFFF';
    }

    public function getHeaderBackgroundColor(): string
    {
        $default = $this->getBackgroundColor();
        $variant = $this->wpService->getThemeMod('header_background') ?? null;

        if ($variant) {
            return $this->wpService->getThemeMod("color_palette_{$variant}")['base'] ?? $default;
        }

        return $default;
    }

    public function getFooterBackgroundColor(): string
    {
        return $this->wpService->getThemeMod('footer_background')['background-color'] ?? '#FFFFFF';
    }

    public function getLogoSrc(): string
    {
        return
            $this->wpService->getThemeMod('logotype') ?:
            $this->wpService->getStylesheetDirectoryUri() . '/assets/images/municipio.svg';
    }

    public function getTextColor(): string
    {
        return $this->wpService->getThemeMod('color_text')['base'] ?? '#000000';
    }

    public function getFooterTextColor(): string
    {
        return $this->wpService->getThemeMod('footer_color_text');
    }
}
