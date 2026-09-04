<?php

declare(strict_types=1);

namespace Municipio\Helper\Constant;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ConstantTest extends TestCase {
    
    #[TestDox('defined returns true if the constant is defined (uses well known constant)')]
    public function testDefined(): void {
        $constant = new Constant();
        $this->assertTrue($constant->defined('PHP_VERSION'));
    }

    #[TestDox('defined returns false if the constant is not defined')]
    public function testNotDefined(): void {
        $constant = new Constant();
        $this->assertFalse($constant->defined('THIS_CONSTANT_DOES_NOT_EXIST'));
    }

    #[TestDox('constant throws an exception if the constant is not defined')]
    public function testConstantNotDefined(): void {
        $constant = new Constant();
        $this->expectException(\Error::class);
        $constant->constant('THIS_CONSTANT_DOES_NOT_EXIST');
    }

    #[TestDox('constant returns the value of a defined constant')]
    public function testConstantDefined(): void {
        define('TEST_CONSTANT', 'test_value');
        $constant = new Constant();
        $this->assertSame('test_value', $constant->constant('TEST_CONSTANT'));
    }
}