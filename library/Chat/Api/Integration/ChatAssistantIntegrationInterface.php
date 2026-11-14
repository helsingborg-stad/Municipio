<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Integration;

use Municipio\Chat\PIIRedactor\RedactionResult;

/**
 * Defines integration-specific chat provider behavior.
 */
interface ChatAssistantIntegrationInterface
{
    /**
     * Validates assistant configuration required by the integration.
     *
     * @param array $assistant Assistant configuration payload.
     * @return \WP_Error|null A WP_Error when config is invalid, otherwise null.
     */
    public function validateAssistantConfig(array $assistant): ?\WP_Error;

    /**
     * Registers the response handling strategy for a chat request.
     *
     * @param \WP_REST_Request $request The incoming REST request.
     * @param array $assistant Assistant configuration payload.
     * @param array $params Raw request params.
     * @param RedactionResult $redaction The redaction result.
     */
    public function registerResponseHandler(
        \WP_REST_Request $request,
        array $assistant,
        array $params,
        RedactionResult $redaction,
    ): void;
}
