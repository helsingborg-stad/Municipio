<?php

namespace Municipio\MirroredPost\Utils\GetOtherBlogId;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use WpService\Implementations\FakeWpService;

class GetOtherBlogIdTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $getOtherBlogId = new GetOtherBlogId(new FakeWpService());

        $this->assertInstanceOf(GetOtherBlogId::class, $getOtherBlogId);
    }

    /**
     * @testdox getOtherBlogId method returns the correct blog ID when is in switched mode
     */
    public function testGetOtherBlogIdReturnsCorrectBlogIdInSwitchedMode(): void
    {
        $wpService      = new FakeWpService(['isMultisite' => true, 'msIsSwitched' => true, 'getCurrentBlogId' => 2]);
        $getOtherBlogId = new GetOtherBlogId($wpService);

        $this->assertEquals(2, $getOtherBlogId->getOtherBlogId());
    }

    /**
     * @testdox getOtherBlogId method the correct blog ID when parameters are set
     */
    public function testGetOtherBlogIdReturnsCorrectBlogIdWhenParametersSet(): void
    {
        $wpService      = new FakeWpService(['isMultisite' => true, 'msIsSwitched' => false, 'getQueryVar' => $this->getQueryVarFake(3, 1)]);
        $getOtherBlogId = new GetOtherBlogId($wpService);

        $this->assertEquals(3, $getOtherBlogId->getOtherBlogId());
    }

    /**
     * @testdox getOtherBlogId method returns null when not in switched mode and no parameters are set
     */
    public function testGetOtherBlogIdReturnsNullWhenNotInSwitchedModeAndNoParametersSet(): void
    {
        $wpService      = new FakeWpService(['isMultisite' => false, 'msIsSwitched' => false, 'getQueryVar' => $this->getQueryVarFake()]);
        $getOtherBlogId = new GetOtherBlogId($wpService);

        $this->assertNull($getOtherBlogId->getOtherBlogId());
    }

    /**
     * @testdox getOtherBlogId method returns null only post ID is set and no blog ID is provided
     */
    public function testGetOtherBlogIdReturnsNullWhenPostIdIsSetAndNoBlogIdIsProvided(): void
    {
        $wpService      = new FakeWpService(['isMultisite' => true, 'msIsSwitched' => false, 'getQueryVar' => $this->getQueryVarFake(null, 1)]);
        $getOtherBlogId = new GetOtherBlogId($wpService);

        $this->assertNull($getOtherBlogId->getOtherBlogId());
    }

    private function getQueryVarFake(?int $blogId = null, ?int $postId = null): callable
    {
        return fn($var, $default) => match ($var) {
            'p' => $postId,
            BlogIdQueryVar::BLOG_ID_QUERY_VAR => $blogId,
            default => $default,
        };
    }
}
