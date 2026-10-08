<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

/**
 * Resolves provider implementations from a request parameter.
 *
 * Falls back to the configured default provider when the parameter is absent,
 * invalid, or not mapped.
 */
class RequestParamConversationApiProviderFactory implements ConversationApiProviderFactoryInterface
{
    /**
     * @param ConversationApiProviderInterface $defaultProvider Provider used as fallback.
     * @param array<string, ConversationApiProviderInterface> $providerMap Provider map keyed by normalized identifiers.
     * @param string $providerParamName Request parameter name containing provider identifier.
     */
    public function __construct(
        private ConversationApiProviderInterface $defaultProvider,
        private array $providerMap,
        private string $providerParamName = 'provider',
    ) {
    }

    /**
     * @param array<string, mixed> $requestParams Incoming REST request parameters.
     * @return ConversationApiProviderInterface Selected provider or fallback default provider.
     */
    public function createProvider(array $requestParams): ConversationApiProviderInterface
    {
        $candidateProviderId = $requestParams[$this->providerParamName] ?? null;

        if (!is_string($candidateProviderId) || trim($candidateProviderId) === '') {
            return $this->defaultProvider;
        }

        $normalizedProviderId = strtolower(trim($candidateProviderId));

        return $this->providerMap[$normalizedProviderId] ?? $this->defaultProvider;
    }
}
