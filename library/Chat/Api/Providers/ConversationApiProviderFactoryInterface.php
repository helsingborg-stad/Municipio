<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

/**
 * Resolves a conversation API provider for a request context.
 */
interface ConversationApiProviderFactoryInterface
{
    /**
     * Resolves a provider for the current request.
     *
     * @param array<string, mixed> $requestParams Incoming REST request parameters.
     * @return ConversationApiProviderInterface Selected provider implementation.
     */
    public function createProvider(array $requestParams): ConversationApiProviderInterface;
}
