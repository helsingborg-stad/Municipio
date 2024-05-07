<?php

namespace Municipio\BrandedEmails;

use Municipio\BrandedEmails\Config\GetMailFromName;
use WpService\Contracts\AddFilter;

class SetMailFromName implements Hookable
{
    public function __construct(private GetMailFromName $configService, private AddFilter $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_mail_from_name', [$this, 'getMailFromName']);
    }

    public function getMailFromName($fromName): string
    {
        return $this->configService->getMailFromName() ?? $fromName;
    }
}
