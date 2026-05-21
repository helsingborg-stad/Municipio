<?php

namespace Municipio\Chat\Api;

use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\Chat\Api\ChatEndpoint;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\HooksRegistrar\Hookable;

class RegisterChatEndpoint implements Hookable
{
    public function __construct(
        private ChatEndpoint $endpoint,
        private ChatConfigInterface $config,
    ) {}

    public function addHooks(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        RestApiEndpointsRegistry::add($this->endpoint);
    }
}
