<?php

declare(strict_types=1);

namespace Municipio\Chat\Contracts;

/**
 * Generic interface for backend chat conversation providers.
 */
interface ConversationProviderInterface
{
    /**
     * Handles one conversation request and yields normalized events.
     *
     * @param ConversationRequest $request Conversation request payload.
     * @return \Generator<int, ConversationEvent> Stream of normalized events.
     */
    public function streamConversation(ConversationRequest $request): \Generator;
}
