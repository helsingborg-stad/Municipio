<?php

namespace Municipio\BrandedEmails\Config;

use AcfService\Contracts\GetField;

class BrandedEmailsConfigService implements BrandedEmailsConfig
{
    public const OPTION_ENABLED_KEY = 'mun_branded_emails_enabled';

    public function __construct(private GetField $acfService)
    {
    }

    public function isEnabled(): bool
    {
        $value = $this->acfService->getField(self::OPTION_ENABLED_KEY, 'option');
        return (int)$value === 1; // $value can be true, "1" or 1, otherwise false
    }
}
