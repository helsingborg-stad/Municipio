<?php

declare(strict_types=1);

namespace Municipio\Chat\Api\Providers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AiProtocolProfileTest extends TestCase
{
    #[TestDox('getValidSseEventNames() returns the expected AI SSE event allow-list')]
    public function testGetValidSseEventNamesReturnsExpectedValues(): void
    {
        static::assertSame(
            ['first_chunk', 'text', 'tool_call', 'error'],
            AiProtocolProfile::getValidSseEventNames(),
        );
    }

    #[TestDox('getValidSseResponseKeys() returns the expected AI SSE payload key allow-list')]
    public function testGetValidSseResponseKeysReturnsExpectedValues(): void
    {
        static::assertSame(
            ['session_id', 'answer', 'error'],
            AiProtocolProfile::getValidSseResponseKeys(),
        );
    }

    #[TestDox('COMMUNICATION_FAILURE_CODE remains stable for API consumers')]
    public function testCommunicationFailureCodeIsStable(): void
    {
        static::assertSame(
            'chat_api_communication_failed',
            AiProtocolProfile::COMMUNICATION_FAILURE_CODE,
        );
    }
}
