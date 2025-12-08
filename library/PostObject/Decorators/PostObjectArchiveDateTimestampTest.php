<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Date\TimestampResolverInterface;
use Municipio\PostObject\PostObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectArchiveDateTimestampTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $resolver  = $this->createMock(TimestampResolverInterface::class);
        $decorator = new PostObjectArchiveDateTimestamp(new PostObject(1, new FakeWpService()), $resolver);
        $this->assertInstanceOf(PostObjectArchiveDateTimestamp::class, $decorator);
    }

    #[TestDox('getDateTimestamp returns unix timestamp')]
    public function testReturnUnixTimestamp()
    {
        $resolver = $this->createMock(TimestampResolverInterface::class);
        $resolver->method('resolve')->willReturn(123);

        $decorator = new PostObjectArchiveDateTimestamp(new PostObject(1, new FakeWpService()), $resolver);

        $this->assertEquals(123, $decorator->getArchiveDateTimestamp());
    }
}
