<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Date\ArchiveDateSourceResolverInterface;
use Municipio\PostObject\PostObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectArchiveDateFormatTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $resolver  = $this->createMock(ArchiveDateSourceResolverInterface::class);
        $decorator = new PostObjectArchiveDateFormat(new PostObject(new FakeWpService()), $resolver);
        $this->assertInstanceOf(PostObjectArchiveDateFormat::class, $decorator);
    }

    /**
     * @testdox getDateTimestamp returns unix timestamp
     */
    public function testReturnsFormat()
    {
        $resolver = $this->createMock(ArchiveDateSourceResolverInterface::class);
        $resolver->method('resolve')->willReturn('H:i');

        $decorator = new PostObjectArchiveDateFormat(new PostObject(new FakeWpService()), $resolver);

        $this->assertEquals('H:i', $decorator->getArchiveDateFormat());
    }
}
