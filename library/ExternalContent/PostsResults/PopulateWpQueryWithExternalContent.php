<?php

namespace Municipio\ExternalContent\PostsResults;

use Municipio\ExternalContent\PostsResults\Helpers\GetSourcesByPostType;
use Municipio\ExternalContent\PostsResults\Helpers\IsQueryForExternalContent;
use Municipio\ExternalContent\SchemaObjectToWpPost\ApplyDefaultProperties;
use Municipio\ExternalContent\SchemaObjectToWpPost\ApplySchemaObjectPropertiesToWpPost;
use Municipio\ExternalContent\Sources\ISource;
use Municipio\HooksRegistrar\Hookable;
use WP_Post;
use WP_Query;
use WpService\Contracts\AddFilter;
use WpService\Contracts\CacheSet;

/**
 * Add external content to wp cache.
 * This is a decorator for the posts_results filter that is used to cache external content.
 *
 * It is necessary to cache external content to make sure that it is available when validating posts with get_post()
 * later in the request.
 */
class PopulateWpQueryWithExternalContent implements Hookable, PostsResultsDecorator
{
    public function __construct(
        private AddFilter&CacheSet $wpService,
        private IsQueryForExternalContent&GetSourcesByPostType $helpers,
        private ApplySchemaObjectPropertiesToWpPost $applySchemaObjectPropertiesToWpPost
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('posts_results', [$this, 'apply'], 10, 2);
    }

    public function apply(array $posts, WP_Query $query): array
    {
        if (!$this->helpers->isQueryForExternalContent($query)) {
            return $posts;
        }

        $sources          = $this->helpers->getSourcesByPostType($query->get('post_type'));
        $postsFromSources = [];

        foreach ($sources as $source) {
            $postsFromSources = array_merge($postsFromSources, $this->getPostsFromSource($source));
        }

        return array_merge($posts, $postsFromSources);
    }

    private function getPostsFromSource(ISource $source): array
    {
        return array_map(
            fn ($schemaObject) =>
            $this->applySchemaObjectPropertiesToWpPost->apply(new WP_Post((object)[]), $schemaObject),
            $source->getObjects()
        );
    }
}
