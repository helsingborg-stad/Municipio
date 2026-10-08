<?php

declare(strict_types=1);

namespace Municipio\Chat\Contracts;

/**
 * Value object describing a normalized conversation stream event.
 */
final readonly class ConversationEvent
{
    /**
     * @param string $type Event type, for example text, activity, done or error.
     * @param array<string, mixed> $payload Event payload data.
     */
    public function __construct(
        public string $type,
        public array $payload = [],
    ) {
    }
}
