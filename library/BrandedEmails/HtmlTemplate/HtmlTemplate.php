<?php

namespace Municipio\BrandedEmails\HtmlTemplate;

interface HtmlTemplate
{
    public function getHeader(): string;
    public function getFooter(): string;
}
