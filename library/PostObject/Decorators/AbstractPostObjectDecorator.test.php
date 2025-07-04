<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractPostObjectDecoratorTest extends TestCase
{
    /**
     * @testdox class can be extended and instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(AbstractPostObjectDecorator::class, $this->getInstance());
    }

    /**
     * @testdox throws exception for non-existent methods
     */
    public function testThrowsExceptionForNonExistentMethods()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method nonExistentMethod does not exist');

        $this->getInstance()->nonExistentMethod();
    }

    private function getInstance(): AbstractPostObjectDecorator
    {
        return new class ($this->getPostObject()) extends AbstractPostObjectDecorator {
            public function __construct(PostObjectInterface $postObject)
            {
                parent::__construct($postObject);
            }
        };
    }

    private function getPostObject(): PostObjectInterface|MockObject
    {
        return $this->createMock(PostObjectInterface::class);
    }
}
