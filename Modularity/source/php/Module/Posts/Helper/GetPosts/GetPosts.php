<?php

namespace Modularity\Module\Posts\Helper\GetPosts;

use Modularity\Helper\WpQueryFactory\WpQueryFactoryInterface;
use Modularity\Module\Posts\Helper\GetPosts\GetPostsInterface;
use Modularity\Module\Posts\Helper\GetPosts\PostsResult;
use Modularity\Module\Posts\Helper\GetPosts\PostsResultInterface;
use Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType\PostTypesFromSchemaTypeResolverInterface;
use WpService\Contracts\{
    GetPermalink,
    GetPostType,
    GetTheID,
    IsArchive,
    IsUserLoggedIn,
};

class GetPosts implements GetPostsInterface
{
    public function __construct(
        private array $fields,
        private int $page,
        private \Municipio\StickyPost\Helper\GetStickyOption|null $getStickyOption,
        private IsUserLoggedIn&GetPermalink&GetPostType&IsArchive&GetTheID $wpService,
        private WpQueryFactoryInterface $wpQueryFactory,
        private PostTypesFromSchemaTypeResolverInterface $postTypesFromSchemaTypeResolver
    )
    {
    }

    /**
     * Get posts and pagination data
     * 
     * @param array $fields
     * @param int $page
     * @return PostsResultInterface
     */
    public function getPosts() :PostsResultInterface
    {
        $stickyPostIds       = $this->getStickyPostIds($this->fields, $this->page);
        $stickyPosts         = $this->getStickyPostsForSite($this->fields, $this->page, $stickyPostIds);
        $wpQuery     = $this->wpQueryFactory->create($this->getPostArgs($this->fields, $this->page, $stickyPostIds));
        $stickyPosts = $this->sortPosts($stickyPosts, $this->fields['posts_sort_by'] ?? 'date', $this->fields['posts_sort_order'] ?? 'desc');
        $posts = $wpQuery->get_posts();

        return $this->formatResponse(
            $posts,
            $wpQuery->max_num_pages,
            $stickyPosts
        );
    }

    /**
     * Format the response
     * 
     * @param array $posts
     * @param int $maxNumPages
     * @param array $stickyPosts
     * @return PostsResultInterface
     */
    private function formatResponse(array $posts, int $maxNumPages, array $stickyPosts): PostsResultInterface
    {
        return new PostsResult( $posts, $maxNumPages, $stickyPosts );
    }

    /**
     * Get sticky posts for site
     */
    private function getStickyPostsForSite(array $fields, int $page, array $stickyPostIds): array
    {
        if (
            empty($stickyPostIds) ||
            empty($fields['posts_data_source']) ||
            $fields['posts_data_source'] !== 'posttype' ||
            empty($fields['posts_data_post_type']) ||
            $page !== 1
        ) {
            return [];
        }

        if (array_key_exists($currentPostID = $this->getCurrentPostID(), $stickyPostIds)) {
            unset($stickyPostIds[$currentPostID]);
        }

        $args = $this->getDefaultQueryArgs();
        $args['post_type'] = $fields['posts_data_post_type'];
        $args['post__in'] = array_values($stickyPostIds);
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
        $args['posts_per_page'] = $this->getPostsPerPage($fields);

        $args['post_status'] = ['publish', 'inherit'];
        if ($this->wpService->isUserLoggedIn()) {
            $args['post_status'][] = 'private';
        }

        $wpQuery = $this->wpQueryFactory->create($args);

        return $wpQuery->get_posts();
    }

    /**
     * Get sticky post IDs
     */
    private function getStickyPostIds(array $fields, int $page): array 
    {
        $stickyPosts = [];
        if (is_null($this->getStickyOption) || !empty($fields['posts_data_post_type'])) {
            $stickyPosts = $this->getStickyOption->getOption($fields['posts_data_post_type']);
        }

        return $stickyPosts;
    }

    /**
     * Get post args
     */
    private function getPostArgs(array $fields, int $page, array $stickyPostIds = [])
    {
        $metaQuery        = false;
        $orderby          = !empty($fields['posts_sort_by']) ? $fields['posts_sort_by'] : 'date';
        $order            = !empty($fields['posts_sort_order']) ? $fields['posts_sort_order'] : 'desc';

        // Get post args
        $getPostsArgs = $this->getDefaultQueryArgs();

        // Sort by meta key
        if (strpos($orderby, '_metakey_') === 0) {
            $orderby_key = substr($orderby, strlen('_metakey_'));
            $orderby = 'order_clause';
            $metaQuery = [
                [
                    'relation' => 'OR',
                    'order_clause' => [
                        'key' => $orderby_key,
                        'compare' => 'EXISTS'
                    ],
                    [
                        'key' => $orderby_key,
                        'compare' => 'NOT EXISTS'
                    ]
                ]
            ];
        }

        if ($orderby != 'false') {
            $getPostsArgs['order'] = $order;
            $getPostsArgs['orderby'] = $orderby;
        }

        // Post statuses
        $getPostsArgs['post_status'] = ['publish', 'inherit'];
        if ($this->wpService->isUserLoggedIn()) {
            $getPostsArgs['post_status'][] = 'private';
        }

        // Taxonomy filter
        if (
            isset($fields['posts_taxonomy_filter']) &&
            $fields['posts_taxonomy_filter'] === true &&
            !empty($fields['posts_taxonomy_type'])
        ) {
            $taxType = $fields['posts_taxonomy_type'];
            $taxValues = (array)$fields['posts_taxonomy_value'];

            foreach ($taxValues as $term) {
                $getPostsArgs['tax_query'][] = [
                    'taxonomy' => $taxType,
                    'field' => 'slug',
                    'terms' => $term
                ];
            }
        }

        // Meta filter
        if (isset($fields['posts_meta_filter']) && $fields['posts_meta_filter'] === true) {
            $metaQuery[] = [
                'key' => $fields['posts_meta_key'] ?? '',
                'value' => [$fields['posts_meta_value'] ?? ''],
                'compare' => 'IN',
            ];
        }

        // Data source
        switch ($fields['posts_data_source'] ?? []) {
            case 'posttype':
                $getPostsArgs['post_type'] = $fields['posts_data_post_type'];
                $postsNotIn = [];
                if ($currentPostID = $this->getCurrentPostID()) {
                    $postsNotIn[] = $currentPostID;
                }

                $postsNotIn = array_merge($postsNotIn, $stickyPostIds);
                $getPostsArgs['post__not_in'] = $postsNotIn;

                break;

            case 'children':
                $getPostsArgs['post_type'] = $this->wpService->getPostType();
                $getPostsArgs['post_parent'] = $fields['posts_data_child_of'];
                break;

            case 'manual':
                $getPostsArgs['post__in'] = $fields['posts_data_posts'];
                if ($orderby == 'false') {
                    $getPostsArgs['orderby'] = 'post__in';
                }
                break;

            case 'schematype':
                if(empty($fields['posts_data_schema_type'])) {
                    break;
                }
                $getPostsArgs['post_type'] = $this->postTypesFromSchemaTypeResolver->resolve($fields['posts_data_schema_type']);
                break;
        }

        // Add metaquery to args
        if ($metaQuery) {
            $getPostsArgs['meta_query'] = $metaQuery;
        }

        // Number of posts
        $getPostsArgs['posts_per_page'] = $this->getPostsPerPage($fields);

        // Apply pagination
        $getPostsArgs['paged'] = $page;

        return $getPostsArgs;
    }

    /**
     * Get default query args
     */
    private function getDefaultQueryArgs()
    {
        return [
            'post_type' => 'any',
            'post_password' => false,
            'suppress_filters' => false,
            'ignore_sticky_posts' => true,
        ];
    }

    /**
     * Get posts per page
     */
    private function getPostsPerPage(array $fields): int {
        if (isset($fields['posts_count']) && is_numeric($fields['posts_count'])) {
            return ($fields['posts_count'] == -1 || $fields['posts_count'] > 100) ? 100 : $fields['posts_count'];
        }

        return 10;
    }

    /**
     * Get the current post ID
     */
    public function getCurrentPostID():int|false
    {
        if ($this->wpService->isArchive()) {
            return false;
        }

        return $this->wpService->getTheID();
    }

    /**
     * Sort posts
     * 
     * @param \WP_Post[] $posts
     * @param string $orderby Can be 'date', 'title', 'modified', 'menu_order', 'rand'. Default is 'date'.
     * @param string $order Can be 'asc' or 'desc'. Default is 'desc'. When 'rand' is used, this parameter is ignored.
     */
    public function sortPosts(array $posts, string $orderby = 'date', string $order = 'desc') : array
    {
        usort($posts, fn($a, $b) =>
            match($orderby) {
                'date' => strtotime($a->post_date) > strtotime($b->post_date) ? ($order == 'asc' ? 1 : -1) : ($order == 'asc' ? -1 : 1),
                'title' => $a->post_title > $b->post_title ? ($order == 'asc' ? 1 : -1) : ($order == 'asc' ? -1 : 1),
                'modified' => strtotime($a->post_modified) > strtotime($b->post_modified) ? ($order == 'asc' ? 1 : -1) : ($order == 'asc' ? -1 : 1),
                'menu_order' => $a->menu_order > $b->menu_order ? ($order == 'asc' ? 1 : -1) : ($order == 'asc' ? -1 : 1),
                'rand' => rand(-1, 1),
                default => 0,
            }
        );

        return $posts;
    }
}
