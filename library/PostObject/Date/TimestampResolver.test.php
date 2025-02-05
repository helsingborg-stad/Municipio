<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TimestampResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     * @runInSeparateProcess
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(
            TimestampResolver::class,
            new TimestampResolver(
                $this->createMock(PostObjectInterface::class),
                new FakeWpService(),
                $this->createMock(ArchiveDateSourceResolverInterface::class)
            )
        );
    }

    /**
     * @testdox resolve returns unix value from meta value
     * @runInSeparateProcess
     */
    public function testResolvesMetaKeyValueAndReturnUnix()
    {
        $postObject         = $this->createMock(PostObjectInterface::class);
        $archiveDateSetting = $this->createMock(ArchiveDateSourceResolverInterface::class);

        $resolver = new TimestampResolver($postObject, new FakeWpService(['getPostMeta' => '10 September 2000']), $archiveDateSetting);

        $result = $resolver->resolve();

        $this->assertEquals(968544000, $result);
    }

    /**
     * @testdox resolve returns post published date if meta key is post_date
     * @runInSeparateProcess
     */
    public function testResolvesMetaKeyValueAndReturnPostDatePublished()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getPublishedTime')->willReturn(123);
        $archiveDateSetting = $this->createMock(ArchiveDateSourceResolverInterface::class);
        $archiveDateSetting->method('resolve')->willReturn('post_date');

        $resolver = new TimestampResolver($postObject, new FakeWpService(), $archiveDateSetting);

        $result = $resolver->resolve();

        $this->assertEquals(123, $result);
    }

    /**
     * @testdox resolve returns post modified date if meta key is post_modified
     * @runInSeparateProcess
     */
    public function testResolvesMetaKeyValueAndReturnPostDateModified()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getModifiedTime')->willReturn(123);
        $archiveDateSetting = $this->createMock(ArchiveDateSourceResolverInterface::class);
        $archiveDateSetting->method('resolve')->willReturn('post_modified');

        $resolver = new TimestampResolver($postObject, new FakeWpService(), $archiveDateSetting);

        $result = $resolver->resolve();

        $this->assertEquals(123, $result);
    }

    /**
     * @testdox resolve returns 0 if unable to resolve
     * @runInSeparateProcess
     */
    public function testReturnsZeroIfUnableToConvertToUnix()
    {
        $postObject         = $this->createMock(PostObjectInterface::class);
        $archiveDateSetting = $this->createMock(ArchiveDateSourceResolverInterface::class);

        $resolver = new TimestampResolver($postObject, new FakeWpService(), $archiveDateSetting);

        $result = $resolver->resolve();

        $this->assertEquals(0, $result);
    }
}
