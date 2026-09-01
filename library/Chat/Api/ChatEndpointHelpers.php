<?php

declare(strict_types=1);

namespace Municipio\Chat\Api;

class ChatEndpointHelpers
{
    public static function trimEventPayload(string $sseEvent, array $validEventNames, array $validResponseKeys): ?string
    {
        $parts = explode("\n", $sseEvent);
        $eventType = str_replace('event: ', '', $parts[0]);
        $data = substr($parts[1], 6);

        if (!in_array($eventType, $validEventNames, true)) {
            return null;
        }

        $parsed = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        $filtered = array_filter(
            $parsed,
            static fn($key) => in_array($key, $validResponseKeys, true),
            ARRAY_FILTER_USE_KEY,
        );
        $encoded = json_encode($filtered, JSON_THROW_ON_ERROR);

        return $parts[0] . "\n" . 'data: ' . $encoded;
    }
}
