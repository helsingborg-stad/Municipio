<?php

declare(strict_types=1);

namespace Municipio\Helper\Constant;

use Error;
use PHPUnit\Framework\TestCase;

/**
 * Tests the in-memory constant implementation.
 */
class FakeConstantTest extends TestCase
{
    /**
     * Verify configured names are reported as defined.
     */
    public function testDefinedReturnsWhetherConstantExists(): void
    {
        $constant = new FakeConstant(['DEFINED_CONSTANT' => 'value']);

        $isDefined = $constant->defined('DEFINED_CONSTANT');
        $isMissing = $constant->defined('MISSING_CONSTANT');

        static::assertTrue($isDefined);
        static::assertFalse($isMissing);
    }

    /**
     * Verify configured constant values are returned.
     */
    public function testConstantReturnsConfiguredValue(): void
    {
        $constant = new FakeConstant(['DEFINED_CONSTANT' => 'value']);

        $value = $constant->constant('DEFINED_CONSTANT');

        static::assertSame('value', $value);
    }

    /**
     * Verify retrieving an undefined constant matches PHP behavior.
     */
    public function testConstantThrowsErrorWhenConstantIsUndefined(): void
    {
        $constant = new FakeConstant();

        $this->expectException(Error::class);
        $this->expectExceptionMessage('Undefined constant "MISSING_CONSTANT"');

        $constant->constant('MISSING_CONSTANT');
    }
}