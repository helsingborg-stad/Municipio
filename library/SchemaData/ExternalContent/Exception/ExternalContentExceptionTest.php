<?php

namespace Municipio\SchemaData\ExternalContent\Exception;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ExternalContentExceptionTest extends TestCase
{
    #[TestDox('Can be thrown and caught')]
    public function testCanBeThrown(): void
    {
        try {
            throw new ExternalContentException('Test exception');
        } catch (ExternalContentException $e) {
            $this->assertEquals('Test exception', $e->getMessage());
        }
    }
}
