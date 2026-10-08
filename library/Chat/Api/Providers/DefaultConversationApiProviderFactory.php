<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

/**
 * Default provider factory that always returns the configured primary provider.
 */
class DefaultConversationApiProviderFactory implements ConversationApiProviderFactoryInterface
{
    /**
     * @param ConversationApiProviderInterface $defaultProvider Default provider instance.
     */
    public function __construct(
        private ConversationApiProviderInterface $defaultProvider,
    ) {
    }

    /**
     * @param array<string, mixed> $requestParams
     */
    public function createProvider(array $requestParams): ConversationApiProviderInterface
    {
        return $this->defaultProvider;
    }
}
