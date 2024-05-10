<?php

namespace Municipio\BrandedEmails;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

class SetMailContentType implements Hookable
{
    public function __construct(private string $contentType, private AddFilter $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_mail_content_type', [$this, 'getContentType']);
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
