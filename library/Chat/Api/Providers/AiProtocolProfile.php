<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

/**
 * Defines AI provider protocol constants used for SSE filtering and error signaling.
 */
final class AiProtocolProfile
{
    /**
     * Communication failure code used in provider-originated SSE error events.
     */
    public const COMMUNICATION_FAILURE_CODE = 'chat_api_communication_failed';

    /**
     * Returns valid SSE event names accepted from the upstream AI provider.
     *
     * @return list<string>
     */
    public static function getValidSseEventNames(): array
    {
        return ['first_chunk', 'text', 'tool_call', 'error'];
    }

    /**
     * Returns valid payload keys forwarded to the frontend from upstream AI SSE data.
     *
     * @return list<string>
     */
    public static function getValidSseResponseKeys(): array
    {
        return ['session_id', 'answer', 'error'];
    }
}
