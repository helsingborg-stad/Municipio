<?php

namespace Municipio\MirroredPost\Utils\IsMirroredPost;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetQueryVar;

class IsMirroredPostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_GET = []; // Reset the $_GET superglobal before each test
    }

    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $isMirroredPost = new IsMirroredPost($this->getWpServiceMock());

        $this->assertInstanceOf(IsMirroredPost::class, $isMirroredPost);
    }

    /**
     * @testdox isMirroredPost returns true when post is mirrored
     */
    public function testIsMirroredPostReturnsTrueWhenPostIsMirrored(): void
    {
        $wpService = $this->getWpServiceMock();
        $wpService->method('getQueryVar')->willReturnMap([
            [BlogIdQueryVar::BLOG_ID_QUERY_VAR, null, '1'],
            ['p', null, '1'],
        ]);

        $isMirroredPost = new IsMirroredPost($wpService);

        $this->assertTrue($isMirroredPost->isMirrored());
    }

    /**
     * @testdox isMirroredPost returns false if post is not set in $_GET
     */
    public function testIsMirroredPostReturnsFalseIfPostIs(): void
    {
        $wpService = $this->getWpServiceMock();
        $wpService->method('getQueryVar')->willReturnMap([
            [BlogIdQueryVar::BLOG_ID_QUERY_VAR, null, '1'],
            ['p', null, null],
        ]);

        $isMirroredPost = new IsMirroredPost($wpService);

        $this->assertFalse($isMirroredPost->isMirrored());
    }

    /**
     * @testdox isMirroredPost returns false if blog ID is not set in $_GET
     */
    public function testIsMirroredPostReturnsFalseIfBlogIdIsNotSet(): void
    {
        $wpService = $this->getWpServiceMock();
        $wpService->method('getQueryVar')->willReturnMap([
            [BlogIdQueryVar::BLOG_ID_QUERY_VAR, null, null],
            ['p', null, '1'],
        ]);

        $isMirroredPost = new IsMirroredPost($wpService);

        $this->assertFalse($isMirroredPost->isMirrored());
    }

    /**
     * @testdox isMirroredPost returns false if neither post nor blog ID is set in $_GET
     */
    public function testIsMirroredPostReturnsFalseIfNeitherPostNorBlogIdIsSet(): void
    {
        $wpService = $this->getWpServiceMock();
        $wpService->method('getQueryVar')->willReturnMap([
            [BlogIdQueryVar::BLOG_ID_QUERY_VAR, null, null],
            ['p', null, null],
        ]);

        $isMirroredPost = new IsMirroredPost($wpService);

        $this->assertFalse($isMirroredPost->isMirrored());
    }

    private function getWpServiceMock(): GetQueryVar|MockObject
    {
        return $this->createMock(GetQueryVar::class);
    }
}
