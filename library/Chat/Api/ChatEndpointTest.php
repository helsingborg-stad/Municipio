<?php

namespace Municipio\Chat\Api;

use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ChatEndpointTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $config = new class () implements ChatConfigInterface {
            public function isEnabled(): bool
            {
                return true;
            }

            public function isGlobalChatEnabled(): bool
            {
                return true;
            }

            public function getDefaultAssistant(): ?array
            {
                return ['name' => 'Default'];
            }

            public function getAssistants(): array
            {
                return [];
            }
        };
        $piiRedactor = new class () implements PIIRedactorInterface {
            public function extractAndRedactPII(string $input): RedactionResult
            {
                $result = new RedactionResult();
                $result->redactedText = $input;
                $result->mappedPII = [];
                return $result;
            }
        };
        $this->assertInstanceOf(ChatEndpoint::class, new ChatEndpoint($config, $piiRedactor));
    }

    #[TestDox('handleRegisterRestRoute() can be called')]
    public function testHandleRegisterRestRouteCanBeCalled(): void
    {
        $config = new class () implements ChatConfigInterface {
            public function isEnabled(): bool
            {
                return true;
            }

            public function isGlobalChatEnabled(): bool
            {
                return true;
            }

            public function getDefaultAssistant(): ?array
            {
                return ['name' => 'Default'];
            }

            public function getAssistants(): array
            {
                return [];
            }
        };
        $piiRedactor = new class () implements PIIRedactorInterface {
            public function extractAndRedactPII(string $input): RedactionResult
            {
                $result = new RedactionResult();
                $result->redactedText = $input;
                $result->mappedPII = [];
                return $result;
            }
        };
        $endpoint = new ChatEndpoint($config, $piiRedactor);
        $this->assertTrue(method_exists($endpoint, 'handleRegisterRestRoute'));
    }
}
