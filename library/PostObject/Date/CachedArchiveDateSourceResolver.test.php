<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;

class CachedArchiveDateSourceResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(
            CachedArchiveDateSourceResolver::class,
            new CachedArchiveDateSourceResolver(
                $this->createMock(PostObjectInterface::class),
                $this->createMock(ArchiveDateSourceResolverInterface::class)
            )
        );
    }

    /**
     * @testdox resolve() caches result from inner resolver
     */
    public function testResolveCachesResultFromInnerResolver()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getPostType')->willReturn('post_type');
        $postObject->method('getBlogId')->willReturn(1);
        $innerResolver = $this->createMock(ArchiveDateSourceResolverInterface::class);
        $innerResolver->expects($this->exactly(1))->method('resolve')->willReturn('metaKey');

        $resolver = new CachedArchiveDateSourceResolver($postObject, $innerResolver);

        $result = $resolver->resolve();
        $result = $resolver->resolve();

        $this->assertEquals('metaKey', $result);
    }
}
