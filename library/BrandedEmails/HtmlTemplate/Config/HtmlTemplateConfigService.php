<?php

namespace Municipio\BrandedEmails\HtmlTemplate\Config;

class HtmlTemplateConfigService implements HtmlTemplateConfig
{
    public function getBackgroundColor(): string
    {
        return '#ffffff';
    }

    public function getHeaderBackgroundColor(): string
    {
        return '#ffffff';
    }

    public function getLogoSrc(): string
    {
        return 'https://media.helsingborg.se/uploads/networks/4/sites/198/2023/08/logotype-municipio-union-split-gradient-colorize.svg';
    }

    public function getTextColor(): string
    {
        return '#000000';
    }
}
