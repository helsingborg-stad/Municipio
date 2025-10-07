<?php

namespace Municipio\PostObject\Date;

use Municipio\Helper\StringToTimeInterface;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TimestampResolverTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(
            TimestampResolver::class,
            new TimestampResolver(
                $this->createMock(PostObjectInterface::class),
                new FakeWpService(),
                $this->createMock(ArchiveDateSourceResolverInterface::class),
                $this->createMock(StringToTimeInterface::class)
            )
        );
    }

    #[TestDox('resolve returns unix value from meta value')]
    public function testResolvesMetaKeyValueAndReturnUnix()
    {
        $dateString = '10 Januari 2000';
        \Municipio\Helper\WpService::set(new FakeWpService(['getPostMeta' => $dateString, '__' => 'Januari']));
        $postObject         = $this->createMock(PostObjectInterface::class);
        $archiveDateSetting = $this->createMock(ArchiveDateSourceResolverInterface::class);
        $stringToTime       = $this->createMock(StringToTimeInterface::class);
        $stringToTime->method('convert')->with($dateString)->willReturn(968544000);

        $resolver = new TimestampResolver(
            $postObject,
            new FakeWpService(['getPostMeta' => $dateString]),
            $archiveDateSetting,
            $stringToTime
        );

        $result = $resolver->resolve();

        $this->assertEquals(968544000, $result);
    }

    #[TestDox('resolve returns post published date if meta key is post_date')]
    public function testResolvesMetaKeyValueAndReturnPostDatePublished()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getPublishedTime')->willReturn(123);
        $archiveDateSetting = $this->createMock(ArchiveDateSourceResolverInterface::class);
        $archiveDateSetting->method('resolve')->willReturn('post_date');

        $resolver = new TimestampResolver(
            $postObject,
            new FakeWpService(),
            $archiveDateSetting,
            $this->createMock(StringToTimeInterface::class)
        );

        $result = $resolver->resolve();

        $this->assertEquals(123, $result);
    }

    #[TestDox('resolve returns post modified date if meta key is post_modified')]
    public function testResolvesMetaKeyValueAndReturnPostDateModified()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getModifiedTime')->willReturn(123);
        $archiveDateSetting = $this->createMock(ArchiveDateSourceResolverInterface::class);
        $archiveDateSetting->method('resolve')->willReturn('post_modified');

        $resolver = new TimestampResolver(
            $postObject,
            new FakeWpService(),
            $archiveDateSetting,
            $this->createMock(StringToTimeInterface::class)
        );

        $result = $resolver->resolve();

        $this->assertEquals(123, $result);
    }

    #[TestDox('resolve returns 0 if unable to resolve')]
    public function testReturnsZeroIfUnableToConvertToUnix()
    {
        $postObject         = $this->createMock(PostObjectInterface::class);
        $archiveDateSetting = $this->createMock(ArchiveDateSourceResolverInterface::class);

        $resolver = new TimestampResolver(
            $postObject,
            new FakeWpService(),
            $archiveDateSetting,
            $this->createMock(StringToTimeInterface::class)
        );

        $result = $resolver->resolve();

        $this->assertEquals(0, $result);
    }
}
