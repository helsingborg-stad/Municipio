<?php

namespace Municipio\MirroredPost\Utils\GetOtherBlogId;

use WpService\Implementations\FakeWpService;

class GetOtherBlogIdTest extends \PHPUnit\Framework\TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $getOtherBlogId = new GetOtherBlogId(new FakeWpService());

        $this->assertInstanceOf(GetOtherBlogId::class, $getOtherBlogId);
    }

    #[TestDox('getOtherBlogId method returns the correct blog ID when is in switched mode')]
    public function testGetOtherBlogIdReturnsCorrectBlogIdInSwitchedMode(): void
    {
        $wpService      = new FakeWpService(['isMultisite' => true, 'msIsSwitched' => true, 'getCurrentBlogId' => 2]);
        $getOtherBlogId = new GetOtherBlogId($wpService);

        $this->assertEquals(2, $getOtherBlogId->getOtherBlogId());
    }

    #[TestDox('getOtherBlogId method returns null when not in switched mode and no parameters are set')]
    public function testGetOtherBlogIdReturnsNullWhenNotInSwitchedModeAndNoParametersSet(): void
    {
        $wpService      = new FakeWpService(['isMultisite' => false, 'msIsSwitched' => false, 'getQueryVar' => $this->getQueryVarFake()]);
        $getOtherBlogId = new GetOtherBlogId($wpService);

        $this->assertNull($getOtherBlogId->getOtherBlogId());
    }

    #[TestDox('getOtherBlogId method returns null only post ID is set and no blog ID is provided')]
    public function testGetOtherBlogIdReturnsNullWhenPostIdIsSetAndNoBlogIdIsProvided(): void
    {
        $wpService      = new FakeWpService(['isMultisite' => true, 'msIsSwitched' => false, 'getQueryVar' => $this->getQueryVarFake(null)]);
        $getOtherBlogId = new GetOtherBlogId($wpService);

        $this->assertNull($getOtherBlogId->getOtherBlogId());
    }

    private function getQueryVarFake(?int $postId = null): callable
    {
        return fn($var, $default) => match ($var) {
            'p' => $postId,
            default => $default,
        };
    }
}
