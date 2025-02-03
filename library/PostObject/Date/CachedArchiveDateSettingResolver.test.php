<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CachedArchiveDateSettingResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     * @runInSeparateProcess
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(
            CachedArchiveDateSettingResolver::class,
            new CachedArchiveDateSettingResolver(
                $this->createMock(PostObjectInterface::class),
                new FakeWpService(),
                $this->createMock(ArchiveDateSettingResolverInterface::class)
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
        $postObject->method('getPostType')->willReturn('post_type');
        $postObject->method('getBlogId')->willReturn(1);
        $innerResolver = $this->createMock(ArchiveDateSettingResolverInterface::class);
        $innerResolver->expects($this->exactly(1))->method('resolve')->willReturn('metaKey');

        $resolver = new CachedArchiveDateSettingResolver($postObject, new FakeWpService(), $innerResolver);

        $result = $resolver->resolve();
        $result = $resolver->resolve();

        $this->assertEquals('metaKey', $result);
    }
}
