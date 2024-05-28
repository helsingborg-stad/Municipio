<?php

namespace Municipio\Tests\Helper\Post;

use WP_Mock;
use WP_Mock\Tools\TestCase;
use Municipio\Helper\Post;

/**
 * Class GetFilteredContentTest
 * @group wp_mock
 */
class GetFilteredContentTest extends TestCase
{
    /**
     * @testdox The more tag is removed from content
     */
    public function testMoreTagIsRemovedFromContent()
    {
        $mockPost = $this->mockPost([
            'post_content'                 => '<p>Lead</p><!--more--><p>Body</p>',
            'hasQuicklinksAfterFirstBlock' => false
        ]);

        $filteredContent = Post::getFilteredContent($mockPost);

        $this->assertStringNotContainsString('<!--more-->', $filteredContent);
    }

    /**
     * @testdox Content filter is applied to content and excerpt
     */
    public function testContentFilterIsAppliedToContentAndExcerpt()
    {
        $mockPost = $this->mockPost([
            'post_content'                 => "<p>Excerpt</p><!--more--><p>Body</p>",
            'hasQuicklinksAfterFirstBlock' => false
        ]);

        WP_Mock::expectFilter('the_excerpt', '<p class="lead">Excerpt</p>');
        WP_Mock::expectFilter('the_content', '<p>Body</p>');

        Post::getFilteredContent($mockPost);

        $this->assertConditionsMet();
    }
}
