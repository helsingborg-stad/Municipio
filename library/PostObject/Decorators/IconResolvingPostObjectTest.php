<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\Icon\Resolvers\IconResolverInterface;
use Municipio\PostObject\Icon\Resolvers\NullIconResolver;
use Municipio\PostObject\PostObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class IconResolvingPostObjectTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $decorator = new IconResolvingPostObject(new PostObject(1, new FakeWpService()), new NullIconResolver());
        $this->assertInstanceOf(IconResolvingPostObject::class, $decorator);
    }

    #[TestDox('getIcon() calls provided icon resolver')]
    public function testGetIconCallsProvidedIconResolver()
    {
        $icon         = $this->createMock(IconInterface::class);
        $iconResolver = $this->createMock(IconResolverInterface::class);

        $icon->method('getIcon')->willReturn('test-icon');
        $iconResolver->method('resolve')->willReturn($icon);

        $postObject = new IconResolvingPostObject(new PostObject(1, new FakeWpService()), $iconResolver);

        $this->assertEquals('test-icon', $postObject->getIcon()->getIcon());
    }
}
