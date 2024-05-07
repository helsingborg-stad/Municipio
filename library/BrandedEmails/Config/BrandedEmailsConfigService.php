<?php

namespace Municipio\BrandedEmails\Config;

use AcfService\AcfService;

class BrandedEmailsConfigService implements BrandedEmailsConfig
{
    public function __construct(private AcfService $acfService)
    {
    }

    public function isEnabled(): bool
    {
        $value = $this->acfService->getField('mun_branded_emails_enabled', 'option');
        return in_array($value, ['1', true], true);
    }

    public function getMailFrom(): ?string
    {
        return $this->acfService->getField('mun_branded_emails_get_email_from', 'option') ?: null;
    }

    public function getMailFromName(): ?string
    {
        return $this->acfService->getField('mun_branded_emails_get_email_from_name', 'option') ?: null;
    }
}
