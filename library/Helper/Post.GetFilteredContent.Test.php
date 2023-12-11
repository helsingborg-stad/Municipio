<?php

namespace Municipio\Helper;

use WP_Mock;
use WP_Mock\Tools\TestCase;

/**
 * Test Post::complementObject
 */
class PostGetFilteredContentTest extends TestCase
{
    public function testMoreTagIsRemovedFromContent() {
        $mockPost = $this->mockPost(['post_content' => '<p>Lead</p><!--more--><p>Body</p>']);
        
        $filteredContent = Post::getFilteredContent($mockPost);

        $this->assertStringNotContainsString('<!--more-->', $filteredContent);
    }

    public function testContentFilterIsAppliedToContentAndExcerpt() {
        $mockPost = $this->mockPost(['post_content' => "<p>Excerpt</p><!--more--><p>Body</p>"]);

        WP_Mock::expectFilter('the_content', '<p class="lead">Excerpt</p>');
        WP_Mock::expectFilter('the_content', '<p>Body</p>');

        Post::getFilteredContent($mockPost);

        $this->assertConditionsMet();
    }
}
