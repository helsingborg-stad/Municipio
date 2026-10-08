<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

/**
 * Value object describing an upstream provider request.
 */
final readonly class ProviderRequest
{
    /**
     * @param string $chatUrl Upstream provider URL.
     * @param string $apiKey Upstream provider API key.
     * @param array<string, mixed> $body Upstream request payload.
     */
    public function __construct(
        public string $chatUrl,
        #[\SensitiveParameter]
        public string $apiKey,
        public array $body,
    ) {
    }
}
