<?php

namespace Municipio\Chat\Api;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Municipio\Chat\Config\ChatConfigInterface;
use Municipio\Chat\PIIRedactor\PIIRedactorInterface;
use Municipio\Chat\PIIRedactor\RedactionResult;

class RegisterChatEndpointTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $config = new class () implements ChatConfigInterface {
            public function __construct(private bool $enabled = false)
            {
            }

            public function isEnabled(): bool
            {
                return $this->enabled;
            }

            public function isGlobalChatEnabled(): bool
            {
                return false;
            }

            public function getDefaultAssistant(): ?array
            {
                return null;
            }

            public function getAssistants(): array
            {
                return [];
            }
        };

        $endpoint = new ChatEndpoint(
            $config,
            new class () implements PIIRedactorInterface {
                public function extractAndRedactPII(string $input): RedactionResult
                {
                    $result = new RedactionResult();
                    $result->redactedText = $input;
                    $result->mappedPII = [];
                    return $result;
                }
            }
        );
        $this->assertInstanceOf(RegisterChatEndpoint::class, new RegisterChatEndpoint($endpoint, $config));
    }

    #[TestDox('addHooks() can be called')]
    public function testAddHooksCanBeCalled(): void
    {
        $config = new class () implements ChatConfigInterface {
            public function isEnabled(): bool
            {
                return false;
            }

            public function isGlobalChatEnabled(): bool
            {
                return false;
            }

            public function getDefaultAssistant(): ?array
            {
                return null;
            }

            public function getAssistants(): array
            {
                return [];
            }
        };
        $endpoint = new ChatEndpoint(
            $config,
            new class () implements PIIRedactorInterface {
                public function extractAndRedactPII(string $input): RedactionResult
                {
                    $result = new RedactionResult();
                    $result->redactedText = $input;
                    $result->mappedPII = [];
                    return $result;
                }
            }
        );
        $register = new RegisterChatEndpoint($endpoint, $config);
        $this->assertNull($register->addHooks());
    }
}
