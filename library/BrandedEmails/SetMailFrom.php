<?php

namespace Municipio\BrandedEmails;

use Municipio\BrandedEmails\Config\GetMailFrom;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

class SetMailFrom implements Hookable
{
    public function __construct(private GetMailFrom $configService, private AddFilter $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_mail_from', [$this, 'getMailFrom']);
    }

    public function getMailFrom($fromEmail): string
    {
        return $this->configService->getMailFrom() ?? $fromEmail;
    }
}
