<?php

namespace Municipio\PostObject\Icon\Resolvers;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;

class CachedIconResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $resolver = new CachedIconResolver($this->createMock(PostObjectInterface::class), $this->createMock(IconResolverInterface::class));
        $this->assertInstanceOf(CachedIconResolver::class, $resolver);
    }

    /**
     * @testdox resolve() caches result from inner resolver
     * @runInSeparateProcess
     */
    public function testResolveCachesResultFromInnerResolver()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getId')->willReturn(1);
        $innerResolver = $this->createMock(IconResolverInterface::class);
        $innerResolver->expects($this->once())->method('resolve')->willReturn(null);

        $resolver = new CachedIconResolver($postObject, $innerResolver);

        $resolver->resolve();
        $resolver->resolve();
    }
}
