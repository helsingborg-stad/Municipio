<?php

declare(strict_types=1);

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ApplyIncludedPostIdsTest extends TestCase
{
    #[TestDox('sets post__in arg from included post IDs in config')]
    public function testApply(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function getIncludedPostIds(): array
            {
                return [1, 2, 3];
            }
        };

        $applyIncludedPostIds = new ApplyIncludedPostIds();
        $initialArgs = ['post_type' => 'post'];

        $resultArgs = $applyIncludedPostIds->apply($config, $initialArgs);

        static::assertSame(
            [
                'post_type' => 'post',
                'post__in' => [1, 2, 3],
            ],
            $resultArgs,
        );
    }
}
