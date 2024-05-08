<?php

namespace Municipio\BrandedEmails\HtmlTemplate;

use Municipio\BrandedEmails\HtmlTemplate\Config\HtmlTemplateConfig;

class DefaultHtmlTemplate implements HtmlTemplate
{
    public function __construct(private HtmlTemplateConfig $config)
    {
    }

    public function getHeader(): string
    {
        return file_get_contents(__DIR__ . '/views/Default.Header.php');
    }

    public function getFooter(): string
    {
        return file_get_contents(__DIR__ . '/views/Default.Footer.php');
    }
}
