<?php

namespace Municipio\CommonFieldGroups\SubFieldValueResolver;

use PHPUnit\Framework\TestCase;

class NullResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanInstantiateNullResolver(): void
    {
        $resolver = new NullResolver();
        $this->assertInstanceOf(NullResolver::class, $resolver);
    }

    /**
     * @testdox resolver returns null
     */
    public function testResolveReturnsNull(): void
    {
        $resolver = new NullResolver();
        $result   = $resolver->resolve([], 'test_key');

        $this->assertNull($result);
    }
}
