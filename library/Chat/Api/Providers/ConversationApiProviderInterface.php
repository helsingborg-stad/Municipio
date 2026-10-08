<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

use Municipio\Chat\PIIRedactor\RedactionResult;

/**
 * Interface describing provider-specific request mapping and stream handling.
 */
interface ConversationApiProviderInterface
{
    /**
     * Creates a provider-specific upstream request.
     *
     * @param array<string, mixed> $requestParams Parameters from the REST request.
     * @param RedactionResult $redaction Sanitized and redacted message payload.
    * @return mixed Provider request details or validation error object.
     */
    public function createProviderRequest(array $requestParams, RedactionResult $redaction): mixed;

    /**
     * Streams the provider response to the active HTTP output stream.
     *
     * @param ProviderRequest $providerRequest Upstream provider request details.
     */
    public function streamResponse(ProviderRequest $providerRequest): void;
}
