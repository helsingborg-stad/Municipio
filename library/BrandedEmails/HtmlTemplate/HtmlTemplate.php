<?php

namespace Municipio\BrandedEmails\HtmlTemplate;

interface HtmlTemplate
{
    public function setContent(string $content): void;
    public function setSubject(string $content): void;
    public function getHtml(): string;
}
