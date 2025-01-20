<?php

namespace Municipio\Helper\SiteSwitcher;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SiteSwitcherTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset global state for testing
        global $blog_id;
        $blog_id = 1; // Default blog ID
    }

    protected function tearDown(): void
    {
        // Reset global state after each test
        global $blog_id;
        $blog_id = 1; // Reset to default
    }

    /**
     * @testdox runInSite() switches to the specified site and restores the original site after execution
     */
    public function testRunInSiteSwitchesToSpecifiedSiteAndRestoresOriginalSite()
    {
        global $blog_id;

        $siteSwitcher = new SiteSwitcher(
            new FakeWpService([
            'switchToBlog'       => function ($siteId) {
                global $blog_id;
                $blog_id = $siteId;
                return true;
            },
            'restoreCurrentBlog' => function () {
                global $blog_id;
                $blog_id = 1; // Reset to default
                return false;
            }
            ])
        );

        $originalBlogId = 1; // Mock the original blog ID
        $targetBlogId   = 2;   // Mock the target blog ID
        $blog_id        = $originalBlogId;

        $callableExecuted = false;
        $result           = $siteSwitcher->runInSite(
            $targetBlogId,
            function () use (&$callableExecuted, $targetBlogId) {
                global $blog_id;

                // Assert that the blog ID has switched
                $this->assertEquals($targetBlogId, $blog_id);

                $callableExecuted = true;

                // Return a value to verify callable execution
                return 'Callable executed';
            }
        );

        // Assert that the callable was executed
        $this->assertTrue($callableExecuted);

        // Assert that the original blog ID was restored
        $this->assertEquals($originalBlogId, $blog_id);

        // Assert the callable's return value
        $this->assertEquals('Callable executed', $result);
    }

    /**
     * @testdox runInSite() restores the original site if an exception is thrown
     */
    public function testRunInSiteRestoresOriginalSiteOnException()
    {
        global $blog_id;

        $siteSwitcher = new SiteSwitcher(
            new FakeWpService([
            'switchToBlog'       => function ($siteId) {
                global $blog_id;
                $blog_id = $siteId;
                return true;
            },
            'restoreCurrentBlog' => function () {
                global $blog_id;
                $blog_id = 1; // Reset to default
                return false;
            }
            ])
        );

        $originalBlogId = 1; // Mock the original blog ID
        $targetBlogId   = 2;   // Mock the target blog ID
        $blog_id        = $originalBlogId;

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Test exception');

        try {
            $siteSwitcher->runInSite(
                $targetBlogId,
                function () {
                    global $blog_id;

                    // Assert that the blog ID has switched
                    $this->assertEquals(2, $blog_id);

                    // Throw an exception to simulate failure
                    throw new \RuntimeException('Test exception');
                }
            );
        } finally {
            // Assert that the original blog ID was restored
            $this->assertEquals($originalBlogId, $blog_id);
        }
    }
}
