<?php

namespace Municipio\Chat\Api;

use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\HooksRegistrar\Hookable;

class RegisterChatStatsEndpoint implements Hookable
{
    public function __construct(
        private ChatStatsEndpoint $endpoint,
        private ChatConfigInterface $config,
    ) {
    }

    public function addHooks(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        RestApiEndpointsRegistry::add($this->endpoint);
    }
}
