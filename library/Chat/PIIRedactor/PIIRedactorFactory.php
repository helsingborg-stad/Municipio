<?php

namespace Municipio\Chat\PIIRedactor;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\Passthrough\PassthroughPIIRedactor;
use Municipio\Chat\PIIRedactor\Presidio\PresidioRedactor;
use Municipio\Chat\PIIRedactor\Presidio\PresidioRedactorConfig;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemotePost;
use WpService\Contracts\WpRemoteRetrieveBody;
use WpService\Contracts\WpRemoteRetrieveResponseCode;

class PIIRedactorFactory implements PIIRedactorFactoryInterface
{
    public function __construct(
        private IsWpError&WpRemotePost&WpRemoteRetrieveBody&WpRemoteRetrieveResponseCode $wpService,
    ) {}

    public function create(ChatConfigInterface $config): PIIRedactorInterface
    {
        $presidioEnabled = $config->isPresidioEnabled();

        if ($presidioEnabled) {
            return new PresidioRedactor(
                $this->wpService,
                new PresidioRedactorConfig(
                    $config->getPresidioAnalyzerHost(),
                    $config->getPresidioAnonymizerHost(),
                    $config->getPresidioLanguage(),
                    $config->getPresidioAnonymizerConfig(),
                    $config->getPresidioAllowList(),
                ),
            );
        }

        return new PassthroughPIIRedactor();
    }
}
