<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetPostMeta;

class PostObjectWithSeoRedirectTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $wpService  = $this->createMock(GetPostMeta::class);

        $this->assertInstanceOf(PostObjectWithSeoRedirect::class, new PostObjectWithSeoRedirect($postObject, $wpService));
    }

    #[TestDox('getPermalink returns redirect url if set')]
    public function testGetPermalinkReturnsRedirectUrlIfSet()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $wpService  = $this->createMock(GetPostMeta::class);

        $postObject->method('getId')->willReturn(1);
        $postObject->method('getPermalink')->willReturn('http://example.com/post');

        $wpService->method('getPostMeta')->willReturn('http://example.com/redirect');

        $postObjectWithSeoRedirect = new PostObjectWithSeoRedirect($postObject, $wpService);

        $this->assertEquals('http://example.com/redirect', $postObjectWithSeoRedirect->getPermalink());
    }

    #[TestDox('getPermalink returns original permalink if redirect url is not set')]
    public function testGetPermalinkReturnsOriginalPermalinkIfRedirectUrlIsNotSet()
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $wpService  = $this->createMock(GetPostMeta::class);

        $postObject->method('getId')->willReturn(1);
        $postObject->method('getPermalink')->willReturn('http://example.com/post');

        $wpService->method('getPostMeta')->willReturn('');

        $postObjectWithSeoRedirect = new PostObjectWithSeoRedirect($postObject, $wpService);

        $this->assertEquals('http://example.com/post', $postObjectWithSeoRedirect->getPermalink());
    }
}
