<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CachedTimestampResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     * @runInSeparateProcess
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(
            CachedTimestampResolver::class,
            new CachedTimestampResolver(
                $this->createMock(PostObjectInterface::class),
                new FakeWpService(),
                $this->createMock(TimestampResolverInterface::class)
            )
        );
    }

    /**
     * @testdox resolve() caches result from inner resolver
     * @runInSeparateProcess
     */
    public function testResolveCachesResultFromInnerResolver()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getId')->willReturn(1);
        $postObject->method('getBlogId')->willReturn(1);
        $innerResolver = $this->createMock(TimestampResolverInterface::class);
        $innerResolver->expects($this->exactly(1))->method('resolve')->willReturn(123);

        $resolver = new CachedTimestampResolver($postObject, new FakeWpService(), $innerResolver);

        $result = $resolver->resolve();
        $result = $resolver->resolve();

        $this->assertEquals(123, $result);
    }
}
