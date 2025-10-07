<?php

namespace Municipio\PostObject\Icon\Resolvers;

use PHPUnit\Framework\TestCase;

class NullIconResolverTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(NullIconResolver::class, new NullIconResolver());
    }

    #[TestDox('resolve() returns null')]
    public function testResolveReturnsNull()
    {
        $resolver = new NullIconResolver();
        $this->assertNull($resolver->resolve());
    }
}
