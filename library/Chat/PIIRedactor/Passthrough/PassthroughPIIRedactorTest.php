<?php

namespace Municipio\Chat\PIIRedactor\Passthrough;

use Municipio\Chat\PIIRedactor\RedactionResult;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class PassthroughPIIRedactorTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(PassthroughPIIRedactor::class, new PassthroughPIIRedactor());
    }

    #[TestDox('extractAndRedactPII() returns a RedactionResult')]
    public function testExtractAndRedactPIIReturnsRedactionResult(): void
    {
        $redactor = new PassthroughPIIRedactor();
        $result = $redactor->extractAndRedactPII('Hello world');

        $this->assertInstanceOf(RedactionResult::class, $result);
    }

    #[TestDox('extractAndRedactPII() returns the input unchanged as redactedText')]
    public function testExtractAndRedactPIIReturnsInputUnchanged(): void
    {
        $redactor = new PassthroughPIIRedactor();
        $input = 'My email is test@example.com and my phone is 555-1234.';
        $result = $redactor->extractAndRedactPII($input);

        $this->assertSame($input, $result->redactedText);
    }

    #[TestDox('extractAndRedactPII() handles an empty string')]
    public function testExtractAndRedactPIIHandlesEmptyString(): void
    {
        $redactor = new PassthroughPIIRedactor();
        $result = $redactor->extractAndRedactPII('');

        $this->assertSame('', $result->redactedText);
    }
}
