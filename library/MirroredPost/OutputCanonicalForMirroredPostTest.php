<?php

namespace Municipio\MirroredPost;

use Municipio\MirroredPost\Utils\MirroredPostUtilsInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class OutputCanonicalForMirroredPostTest extends TestCase
{
    public function testClassCanBeInstantiated(): void
    {
        $outputCanonical = new OutputCanonicalForMirroredPost(
            new FakeWpService(),
            $this->getUtils()
        );

        $this->assertInstanceOf(OutputCanonicalForMirroredPost::class, $outputCanonical);
    }

    private function getUtils(): MirroredPostUtilsInterface|MockObject
    {
        return $this->createMock(MirroredPostUtilsInterface::class);
    }

    /**
     * @testdox outputCanonicalTag returns the original URL if the post is not mirrored
     */
    public function testOutputCanonicalTagReturnsOriginalUrlIfNotMirrored(): void
    {
        $utils = $this->getUtils();
        $utils->method('isMirrored')->willReturn(false);
        $outputCanonical = new OutputCanonicalForMirroredPost(new FakeWpService(), $utils);

        $canonicalUrl = 'https://example.com/original-post';
        $result       = $outputCanonical->outputCanonicalTag($canonicalUrl);

        $this->assertEquals($canonicalUrl, $result);
    }

    /**
     * @testdox outputCanonicalTag returns the permalink of the other blog if the post is mirrored
     */
    public function testOutputCanonicalTagReturnsOtherBlogPermalinkIfMirrored(): void
    {
        $otherBlogPostPermalink = 'https://example.com/other-blog-post';
        $wpService              = new FakeWpService([
            'switchToBlog'       => true,
            'restoreCurrentBlog' => true,
            'getPermalink'       => $otherBlogPostPermalink]);
        $utils                  = $this->getUtils();
        $utils->method('isMirrored')->willReturn(true);
        $utils->method('getOtherBlogId')->willReturn(2);

        $outputCanonical = new OutputCanonicalForMirroredPost($wpService, $utils);

        $result = $outputCanonical->outputCanonicalTag('https://example.com/original-post');

        $this->assertEquals($otherBlogPostPermalink, $result);
    }
}
