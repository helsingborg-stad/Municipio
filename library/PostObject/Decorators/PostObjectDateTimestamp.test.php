<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Date\TimestampResolverInterface;
use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\Icon\Resolvers\IconResolverInterface;
use Municipio\PostObject\Icon\Resolvers\NullIconResolver;
use Municipio\PostObject\PostObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectDateTimestampTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $resolver  = $this->createMock(TimestampResolverInterface::class);
        $decorator = new PostObjectDateTimestamp(new PostObject(new FakeWpService()), new FakeWpService(), $resolver);
        $this->assertInstanceOf(PostObjectDateTimestamp::class, $decorator);
    }

    /**
     * @testdox getDateTimestamp returns unix timestamp
     */
    public function testReturnUnixTimestamp()
    {
        $resolver = $this->createMock(TimestampResolverInterface::class);
        $resolver->method('resolve')->willReturn(123);

        $decorator = new PostObjectDateTimestamp(new PostObject(new FakeWpService()), new FakeWpService(), $resolver);

        $this->assertEquals(123, $decorator->getDateTimestamp());
    }
}
