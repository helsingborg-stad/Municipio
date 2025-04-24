<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CachedArchiveDateFormatResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(
            CachedArchiveDateFormatResolver::class,
            new CachedArchiveDateFormatResolver(
                $this->createMock(PostObjectInterface::class),
                $this->createMock(ArchiveDateFormatResolverInterface::class)
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
        $innerResolver = $this->createMock(ArchiveDateFormatResolverInterface::class);
        $innerResolver->expects($this->exactly(1))->method('resolve')->willReturn('date');

        $resolver = new CachedArchiveDateFormatResolver($postObject, $innerResolver);

        $result = $resolver->resolve();
        $result = $resolver->resolve();

        $this->assertEquals('date', $result);
    }
}
