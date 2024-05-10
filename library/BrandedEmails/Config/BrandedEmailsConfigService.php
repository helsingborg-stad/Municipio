<?php

namespace Municipio\BrandedEmails\Config;

use AcfService\Contracts\GetField;

class BrandedEmailsConfigService implements BrandedEmailsConfig
{
    public function __construct(private GetField $acfService)
    {
    }

    public function isEnabled(): bool
    {
        $value = $this->acfService->getField('mun_branded_emails_enabled', 'option');
        return (int)$value === 1; // $value can be true, "1" or 1, otherwise false
    }

    public function getMailFrom(): ?string
    {
        $value = $this->acfService->getField('mun_branded_emails_get_email_from', 'option') ?: null;
        return filter_var($value, FILTER_VALIDATE_EMAIL) ?: null;
    }

    public function getMailFromName(): ?string
    {
        return $this->acfService->getField('mun_branded_emails_get_email_from_name', 'option') ?: null;
    }
}
