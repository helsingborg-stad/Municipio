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

        $this->assertEquals('permalink', $filter->getPermalinkFromOtherSite('permalink', WpMockFactory::createWpPost()));
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
        $this->assertEquals('permalink', $filter->getPermalinkFromOtherSite('permalink', $post));
    }

    /**
     * @testdox getPermalinkFromOtherSite() returns the permalink from the other site.
     */
    public function testGetPermalinkFromOtherSiteReturnsPermalinkFromOtherSite()
    {
        $url       = 'http://example.com/path';
        $wpService = new FakeWpService([
            'isMultisite'        => true,
            'getCurrentBlogId'   => 1,
            'getBlogIdFromUrl'   => 2,
            'getPermalink'       => $url,
            'switchToBlog'       => true,
            'restoreCurrentBlog' => true,
        ]);
        $filter    = new AllowPostsFromOtherSitesToKeepTheirPermalinks($wpService);

        $post = WpMockFactory::createWpPost(['ID' => 1, 'guid' => $url]);
        $this->assertEquals($url, $filter->getPermalinkFromOtherSite('permalink', $post));
    }

    /**
     * @testdox getPermalinkFromOtherSite() allows switching to a blogs different types of URLs.
     * @dataProvider differentTypesOfUrls
     */
    public function testGetPermalinkFromOtherSiteAllowsSwitchingToDifferentTypesOfUrls($domain, $path, $url)
    {
        $wpService = new FakeWpService([
            'isMultisite'        => true,
            'getCurrentBlogId'   => 1,
            'getBlogIdFromUrl'   => 2,
            'getPermalink'       => $url,
            'switchToBlog'       => true,
            'restoreCurrentBlog' => true,
        ]);
        $filter    = new AllowPostsFromOtherSitesToKeepTheirPermalinks($wpService);

        $post = WpMockFactory::createWpPost(['ID' => 1, 'guid' => $url]);
        $filter->getPermalinkFromOtherSite('permalink', $post);
        $this->assertEquals([$domain, $path], $wpService->methodCalls['getBlogIdFromUrl'][0]);
    }

    private function differentTypesOfUrls(): array
    {
        return [
            [
                'domain' => 'example.com',
                'path'   => '/path',
                'url'    => 'http://example.com/path'
            ],
            [
                'domain' => 'example.com:8080',
                'path'   => '/path',
                'url'    => 'http://example.com:8080/path'
            ],
            [
                'domain' => 'sub.example.com',
                'path'   => '/path/',
                'url'    => 'http://sub.example.com/path/'
            ]
        ];
    }
}
