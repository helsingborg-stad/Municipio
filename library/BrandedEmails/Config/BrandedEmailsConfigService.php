<?php

namespace Municipio\BrandedEmails\Config;

use WpService\Contracts\GetOption;

/**
 * Service for managing branded email configurations.
 */
class BrandedEmailsConfigService implements BrandedEmailsConfig
{
    public const OPTION_ENABLED_KEY = 'mun_branded_emails_enabled';

    /**
     * BrandedEmailsConfigService constructor.
     *
     * @param GetOption $wpService
     */
    public function __construct(private GetOption $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return (int)$this->wpService->getOption("options_" . self::OPTION_ENABLED_KEY, false) === 1;
    }
}
