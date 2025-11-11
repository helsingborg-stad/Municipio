<?php

namespace Municipio\PostsList\GetPosts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WP_Query;

class GetPostsUsingWpQueryTest extends TestCase
{
    #[TestDox('uses provided WP_Query to get posts')]
    public function testGetPostsUsesProvidedWpQuery(): void
    {
        $wpQuery = new class extends WP_Query {
            public function get_posts()
            {
                $post     = new WP_Post([]);
                $post->ID = 123;
                return [$post];
            }
        };

        $getPosts = new GetPostsUsingWpQuery($wpQuery);
        $posts    = $getPosts->getPosts();

        $this->assertCount(1, $posts);
        $this->assertEquals(123, $posts[0]->ID);
    }
}
