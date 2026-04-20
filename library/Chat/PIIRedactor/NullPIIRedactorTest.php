<?php

namespace Municipio\Chat\PIIRedactor;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class NullPIIRedactorTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(NullPIIRedactor::class, new NullPIIRedactor());
    }

    #[TestDox('extractAndRedactPII() returns a RedactionResult')]
    public function testExtractAndRedactPIIReturnsRedactionResult(): void
    {
        $redactor = new NullPIIRedactor();
        $result   = $redactor->extractAndRedactPII('Hello world');

        $this->assertInstanceOf(RedactionResult::class, $result);
    }

    #[TestDox('extractAndRedactPII() returns the input unchanged as redactedText')]
    public function testExtractAndRedactPIIReturnsInputUnchanged(): void
    {
        $redactor = new NullPIIRedactor();
        $input    = 'My email is test@example.com and my phone is 555-1234.';
        $result   = $redactor->extractAndRedactPII($input);

        $this->assertSame($input, $result->redactedText);
    }

    #[TestDox('extractAndRedactPII() returns an empty mappedPII array')]
    public function testExtractAndRedactPIIReturnsEmptyMappedPII(): void
    {
        $redactor = new NullPIIRedactor();
        $result   = $redactor->extractAndRedactPII('Some input');

        $this->assertSame([], $result->mappedPII);
    }

    #[TestDox('extractAndRedactPII() handles an empty string')]
    public function testExtractAndRedactPIIHandlesEmptyString(): void
    {
        $redactor = new NullPIIRedactor();
        $result   = $redactor->extractAndRedactPII('');

        $this->assertSame('', $result->redactedText);
        $this->assertSame([], $result->mappedPII);
    }
}
