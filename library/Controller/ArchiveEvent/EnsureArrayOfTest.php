<?php

namespace Municipio\Controller\ArchiveEvent;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stringable;

class EnsureArrayOfTest extends TestCase
{
    #[TestDox("ensures that a value is an array of a specific type")]
    public function testEnsureArrayOf()
    {
        $value  = [new \stdClass(), [], 'test', 1];
        $result = EnsureArrayOf::ensureArrayOf($value, stdClass::class);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(stdClass::class, $result[0]);
    }

    #[TestDox("transform single value to array if correct type")]
    public function testTransformSingleValueToArrayIfCorrectType()
    {
        $value  = new \stdClass();
        $result = EnsureArrayOf::ensureArrayOf($value, stdClass::class);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(stdClass::class, $result[0]);
    }
}
