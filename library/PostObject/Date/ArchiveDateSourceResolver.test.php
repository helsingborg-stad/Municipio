<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ArchiveDateSsourceResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     * @runInSeparateProcess
     */
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(
            ArchiveDateSourceResolver::class,
            new ArchiveDateSourceResolver($this->createMock(PostObjectInterface::class), new FakeWpService())
        );
    }

    /**
     * @testdox resolve() returns archive post date setting
     * @runInSeparateProcess
     */
    public function testResolveReturnsFoundArchiveSetting()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $wpService  = new FakeWpService(['getThemeMod' => 'metaKey']);

        $resolver = new ArchiveDateSourceResolver($postObject, $wpService);

        $result = $resolver->resolve();

        $this->assertEquals('metaKey', $result);
    }
}
