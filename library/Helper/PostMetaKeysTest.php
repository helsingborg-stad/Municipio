<?php

namespace Municipio\Helper;

use PHPUnit\Framework\TestCase;

class PostMetaKeysTest extends TestCase
{
    public function testPostTypeIsPassedAsAQueryParameter(): void
    {
        global $wpdb;
        $originalWpdb = $wpdb ?? null;
        $originalCache = Post::$runtimeCache;
        $postType = "post' OR 1=1 -- ";
        $wpdb = new class {
            public string $postmeta = 'wp_postmeta';
            public string $posts = 'wp_posts';
            public string $query = '';
            public string $parameter = '';

            public function prepare(string $query, string $parameter): string
            {
                $this->query = $query;
                $this->parameter = $parameter;
                return 'prepared query';
            }

            public function get_col(string $query): array
            {
                TestCase::assertSame('prepared query', $query);
                return ['visible', '_private'];
            }
        };

        try {
            Post::$runtimeCache = [];
            $this->assertSame(['visible'], Post::getPosttypeMetaKeys($postType));
            $this->assertStringContainsString('wp_posts.post_type = %s', $wpdb->query);
            $this->assertStringNotContainsString($postType, $wpdb->query);
            $this->assertSame($postType, $wpdb->parameter);
        } finally {
            $wpdb = $originalWpdb;
            Post::$runtimeCache = $originalCache;
        }
    }
}
