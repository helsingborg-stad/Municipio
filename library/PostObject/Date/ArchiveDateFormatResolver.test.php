<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ArchiveDateFormatResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(
            ArchiveDateFormatResolver::class,
            new ArchiveDateFormatResolver($this->createMock(PostObjectInterface::class), new FakeWpService())
        );
    }

    /**
     * @testdox resolve() returns archive post date setting
     */
    public function testResolveReturnsFoundArchiveSetting()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $wpService  = new FakeWpService(['getThemeMod' => 'date-time']);

        $resolver = new ArchiveDateFormatResolver($postObject, $wpService);

        $result = $resolver->resolve();

        $this->assertEquals('Y-m-d H:i', $result);
    }
}
