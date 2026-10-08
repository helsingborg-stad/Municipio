<?php

declare(strict_types=1);

namespace Municipio\Chat\Contracts;

/**
 * Value object describing a generic conversation request.
 */
final readonly class ConversationRequest
{
    /**
     * @param string $message End-user message payload.
     * @param string|null $assistantName Optional logical assistant identifier.
     * @param string|null $sessionId Optional provider session identifier.
     */
    public function __construct(
        public string $message,
        public ?string $assistantName = null,
        public ?string $sessionId = null,
    ) {
    }
}
