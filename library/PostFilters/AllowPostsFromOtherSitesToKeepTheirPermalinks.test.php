<?php

namespace Municipio\PostFilters;

use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class AllowPostsFromOtherSitesToKeepTheirPermalinksTest extends TestCase
{
    /**
     * @testdox getPermalinkFromOtherSite() attaches the filter to the 'post_link' filter.
     */
    public function testGetPermalinkFromOtherSiteAttachesFilterToPostLink()
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $filter    = new AllowPostsFromOtherSitesToKeepTheirPermalinks($wpService);

        $filter->addHooks();

        $this->assertEquals('post_link', $wpService->methodCalls['addFilter'][0][0]);
    }

    /**
     * @testdox getPermalinkFromOtherSite() returns the permalink if the site is not a multisite.
     */
    public function testGetPermalinkFromOtherSiteReturnsPermalinkIfSiteIsNotMultisite()
    {
        $wpService = new FakeWpService(['isMultisite' => false]);
        $filter    = new AllowPostsFromOtherSitesToKeepTheirPermalinks($wpService);

        $this->assertEquals('permalink', $filter->getPermalinkFromOtherSite('permalink', WpMockFactory::createWpPost(), 'leavename'));
    }

    /**
     * @testdox getPermalinkFromOtherSite() returns the permalink if the site is the same as the current site.
     */
    public function testGetPermalinkFromOtherSiteReturnsPermalinkIfSiteIsSameAsCurrentSite()
    {
        $wpService = new FakeWpService([
            'isMultisite'      => true,
            'getCurrentBlogId' => 1,
            'getBlogIdFromUrl' => 1,
        ]);
        $filter    = new AllowPostsFromOtherSitesToKeepTheirPermalinks($wpService);

        $post = WpMockFactory::createWpPost(['guid' => 'http://example.com/path']);
        $this->assertEquals('permalink', $filter->getPermalinkFromOtherSite('permalink', $post, 'leavename'));
    }

    /**
     * @testdox getPermalinkFromOtherSite() returns the permalink from the other site.
     */
    public function testGetPermalinkFromOtherSiteReturnsPermalinkFromOtherSite()
    {
        $wpService = new FakeWpService([
            'isMultisite'        => true,
            'getCurrentBlogId'   => 1,
            'getBlogIdFromUrl'   => 2,
            'getPermalink'       => 'http://example.com/other-path',
            'switchToBlog'       => true,
            'restoreCurrentBlog' => true,
        ]);
        $filter    = new AllowPostsFromOtherSitesToKeepTheirPermalinks($wpService);

        $post = WpMockFactory::createWpPost(['ID' => 1, 'guid' => 'http://example.com/other-path']);
        $this->assertEquals('http://example.com/other-path', $filter->getPermalinkFromOtherSite('permalink', $post, 'leavename'));
    }
}
