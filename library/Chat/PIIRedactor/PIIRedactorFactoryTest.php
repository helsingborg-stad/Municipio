<?php

namespace Municipio\Chat\PIIRedactor;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class PIIRedactorFactoryTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(PIIRedactorFactory::class, new PIIRedactorFactory());
    }

    #[TestDox('create() returns a PIIRedactorInterface')]
    public function testCreateReturnsPIIRedactorInterface(): void
    {
        $factory = new PIIRedactorFactory();

        $this->assertInstanceOf(PIIRedactorInterface::class, $factory->create());
    }

    #[TestDox('create() returns a NullPIIRedactor by default')]
    public function testCreateReturnsNullPIIRedactor(): void
    {
        $factory = new PIIRedactorFactory();

        $this->assertInstanceOf(NullPIIRedactor::class, $factory->create());
    }
}
