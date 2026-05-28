<?php

namespace Municipio\Chat\PIIRedactor;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests for RedactionResult.
 */
class RedactionResultTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(RedactionResult::class, new RedactionResult());
    }

    #[TestDox('public properties can hold redacted text and mapped pii')]
    public function testPublicPropertiesCanHoldRedactedTextAndMappedPii(): void
    {
        $result = new RedactionResult();

        $result->redactedText = 'My email is [EMAIL_1]';
        $result->mappedPII    = [
            '[EMAIL_1]' => 'john@example.com',
        ];

        $this->assertSame('My email is [EMAIL_1]', $result->redactedText);
        $this->assertSame(['[EMAIL_1]' => 'john@example.com'], $result->mappedPII);
    }
}
