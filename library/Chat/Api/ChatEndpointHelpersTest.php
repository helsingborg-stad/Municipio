<?php

declare(strict_types=1);

namespace Municipio\Chat\Api;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ChatEndpointHelpersTest extends TestCase
{
    #[TestDox('trimEventPayload() returns null for invalid event names')]
    public function testTrimEventPayloadReturnsNullForInvalidEventNames(): void
    {
        $sseEvent = "event: invalid\n" . 'data: {"key1":"value1","key2":"value2"}';
        $validEventNames = ['message'];
        $validResponseKeys = ['key1'];

        $this->assertNull(ChatEndpointHelpers::trimEventPayload($sseEvent, $validEventNames, $validResponseKeys));
    }

    #[TestDox('trimEventPayload() removes invalid keys from the data payload')]
    public function testTrimEventPayloadRemovesInvalidKeysFromDataPayload(): void
    {
        $sseEvent = "event: message\n" . 'data: {"key1":"value1","key2":"value2"}';
        $validEventNames = ['message'];
        $validResponseKeys = ['key1'];

        $expected = "event: message\n" . 'data: {"key1":"value1"}';
        $this->assertSame($expected, ChatEndpointHelpers::trimEventPayload($sseEvent, $validEventNames, $validResponseKeys));
    }
}
