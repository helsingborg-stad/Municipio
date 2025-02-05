<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Date\ArchiveDateFormatResolver;
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
        $resolver  = $this->createMock(ArchiveDateFormatResolver::class);
        $decorator = new PostObjectArchiveDateFormat(new PostObject(new FakeWpService()), $resolver);
        $this->assertInstanceOf(PostObjectArchiveDateFormat::class, $decorator);
    }

    /**
     * @testdox getArchiveDateFormat returns format
     */
    public function testReturnsFormat()
    {
        $resolver = $this->createMock(ArchiveDateFormatResolver::class);
        $resolver->method('resolve')->willReturn('date');

        $decorator = new PostObjectArchiveDateFormat(new PostObject(new FakeWpService()), $resolver);

        $this->assertEquals('date', $decorator->getArchiveDateFormat());
    }
}
