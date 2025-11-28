<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ArchiveDateSourceResolverTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(
            ArchiveDateSourceResolver::class,
            new ArchiveDateSourceResolver($this->createMock(PostObjectInterface::class), new FakeWpService())
        );
    }

    #[TestDox('resolve() returns archive post date setting')]
    public function testResolveReturnsFoundArchiveSetting()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $wpService  = new FakeWpService(['getThemeMod' => 'metaKey']);

        $resolver = new ArchiveDateSourceResolver($postObject, $wpService);

        $result = $resolver->resolve();

        $this->assertEquals('metaKey', $result);
    }
}
