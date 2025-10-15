<?php

namespace Modularity\Module\Posts\Helper\GetPosts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post;

class PostsResultTest extends TestCase {
    public function testClassCanbeInstantiated(): void {
        $postsResult = new PostsResult([], 1, []);

        $this->assertInstanceOf(PostsResult::class, $postsResult);
    }

    public function testGetPostsReturnsOfPosts(): void {
        $posts = [ new WP_Post([]) ];
        $postsResult = new PostsResult($posts, 1, []);

        $this->assertSame($posts, $postsResult->getPosts());
    }

    #[TestDox('instantiation throws if posts contain anything other than WP_Post')]
    public function testGetPostsThrowsExceptionIfNotWPPost(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Posts must be an array of WP_Post objects.');
        new PostsResult([ 'not-a-post' ], 1, []);
    }

    public function testGetNumberOfPagesReturnsInt(): void {
        $postsResult = new PostsResult([], 5, []);

        $this->assertIsInt($postsResult->getNumberOfPages());
        $this->assertSame(5, $postsResult->getNumberOfPages());
    }

    public function testGetStickyPostsReturnsArrayOfPosts(): void {
        $stickyPosts = [ new WP_Post([]) ];
        $postsResult = new PostsResult([], 1, $stickyPosts);

        $this->assertIsArray($postsResult->getStickyPosts());
        $this->assertSame($stickyPosts, $postsResult->getStickyPosts());
    }

    #[TestDox('instantiation throws if sticky posts contain anything other than WP_Post')]
    public function testGetStickyPostsThrowsExceptionIfNotWPPost(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Sticky posts must be an array of WP_Post objects.');
        new PostsResult([], 1, [ 'not-a-post' ]);
    }
}