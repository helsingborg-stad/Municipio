<?php

namespace Modularity\Module\Posts\Helper\GetPosts;

use Modularity\Module\Posts\Helper\GetPosts\GetPostsInterface;
use Modularity\Module\Posts\Helper\GetPosts\PostsResult;
use Modularity\Module\Posts\Helper\GetPosts\PostsResultInterface;
use Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType\PostTypesFromSchemaTypeResolverInterface;
use Modularity\Module\Posts\Helper\GetPosts\UserGroupResolver\UserGroupResolverInterface;
use WpService\Contracts\{
    EscSql,
    GetBlogDetails,
    GetBlogPost,
    GetOption,
    IsUserLoggedIn,
    RestoreCurrentBlog,
    SwitchToBlog,
};

class GetPostsFromMultipleSites implements GetPostsInterface
{
    public function __construct(
        private array $fields,
        private int $page,
        private array $siteIds,
        private \wpdb $wpdb,
        private IsUserLoggedIn&EscSql&GetBlogDetails&SwitchToBlog&RestoreCurrentBlog&GetBlogPost&GetOption $wpService,
        private PostTypesFromSchemaTypeResolverInterface $postTypesFromSchemaTypeResolver,
        private UserGroupResolverInterface $userGroupResolver
    ) {}

    public function getSql(): string {
        return $this->buildUnionSql(array_map(
            fn($site) => $this->buildSiteQuery($site, $this->toSqlList($this->getPostStatuses())),
            $this->getValidSites()
        )) . $this->getOrderBySql();
    }

    public function getPosts(): PostsResultInterface
    {
        $currentBlogId = $this->wpService->getBlogDetails()->blog_id;
        $postStatuses = $this->getPostStatuses();
        $sites = $this->getValidSites();
        
        if (empty($sites)) {
            return $this->formatResponse([], 0, []);
        }

        $dbResults = $this->wpdb->get_results($this->getSql());

        $postsPerPage = $this->getPostsPerPage();
        $offset = ($this->page - 1) * $postsPerPage;
        $maxNumPages = $postsPerPage > 0 ? (int)ceil(count($dbResults) / $postsPerPage) : 0;
        $pagedResults = array_slice($dbResults, $offset, $postsPerPage);

        // Separate sticky and non-sticky posts for clarity and maintainability
        $nonStickyPosts = array_filter( $pagedResults, fn($post) => (int)$post->is_sticky === 0 );
        $stickyPosts = array_filter( $pagedResults, fn($post) => (int)$post->is_sticky === 1 );

        // Fetch post objects for both sticky and non-sticky posts
        $posts = $this->fetchPosts($nonStickyPosts, $postStatuses, $currentBlogId);
        $stickyPostObjects = $this->fetchPosts($stickyPosts, $postStatuses, $currentBlogId);

        // Return formatted response with clear variable names
        return $this->formatResponse($posts, $maxNumPages, $stickyPostObjects);
    }

    private function getPostStatuses(): array
    {
        return $this->wpService->isUserLoggedIn() ? ['publish', 'private'] : ['publish'];
    }

    private function toSqlList(array $items): string
    {
        return implode(',', array_map(
            fn($item) => sprintf("'%s'", $this->wpService->escSql($item)),
            $items
        ));
    }

    private function getValidSites(): array
    {
        $numericSiteIds = array_filter($this->siteIds, 'is_numeric');
        return array_map('intval', $numericSiteIds);   
    }

    private function buildSiteQuery($site, $postStatusesSql): string
    {
        $postsTable = $site == 1 ? "{$this->wpdb->base_prefix}posts" : "{$this->wpdb->base_prefix}{$site}_posts";
        $postMetaTable = $site == 1 ? "{$this->wpdb->base_prefix}postmeta" : "{$this->wpdb->base_prefix}{$site}_postmeta";

        $this->wpService->switchToBlog($site);
        $postTypes = $this->resolvePostTypes();
        $postTypesSql = $this->toSqlList($postTypes);
        $stickyPostIds = $this->getStickyPostIds();
        $this->wpService->restoreCurrentBlog();

        // Prepare a comma-separated list of sticky post IDs for SQL IN clause
        $stickyIdsSql = !empty($stickyPostIds)
            ? implode(',', array_map('intval', $stickyPostIds))
            : '0';

        return "
            SELECT DISTINCT
            '{$site}' AS blog_id,
            posts.ID AS post_id,
            posts.post_date,
            posts.post_title,
            posts.post_modified,
            posts.menu_order,
            CASE WHEN posts.ID IN ($stickyIdsSql) THEN 1 ELSE 0 END AS is_sticky,
            postmeta2.meta_value AS user_group_visibility
            FROM $postsTable posts
            LEFT JOIN $postMetaTable postmeta2 
            ON posts.ID = postmeta2.post_id AND postmeta2.meta_key = 'user-group-visibility'
            WHERE
            posts.post_type IN ($postTypesSql)
            AND posts.post_status IN ($postStatusesSql)
            AND posts.post_date_gmt < NOW()
        ";
    }

    private function resolvePostTypes(): array
    {
        return match ($this->fields['posts_data_source'] ?? null) {
            'posttype' => [$this->fields['posts_data_post_type']],
            'schematype' => !empty($this->fields['posts_data_schema_type'])
                ? $this->postTypesFromSchemaTypeResolver->resolve($this->fields['posts_data_schema_type'])
                : [],
            default => ['post'],
        };
    }

    /**
     * Retrieves sticky post IDs for the resolved post types.
     *
     * @return int[] Array of sticky post IDs.
     */
    private function getStickyPostIds(): array
    {
        $postTypes = $this->resolvePostTypes();

        $stickyIds = [];
        foreach ($postTypes as $postType) {
            $option = $this->wpService->getOption('sticky_post_' . $postType, []);
            if (is_array($option)) {
                $stickyIds = array_merge($stickyIds, $option);
            }
        }

        return array_map('intval', $stickyIds);
    }

    private function buildUnionSql(array $unionQueries): string
    {
        $unionSql = 'SELECT blog_id, post_id, post_date, is_sticky, post_title, post_modified, menu_order, user_group_visibility FROM ('
            . implode(' UNION ', $unionQueries)
            . ') as posts';

        $userGroup = $this->userGroupResolver->getUserGroup();

        if( !empty($userGroup) ) {
            $unionSql .= sprintf(" WHERE user_group_visibility = '%s' OR user_group_visibility IS NULL", $userGroup);
        } else {
            $unionSql .= " WHERE user_group_visibility IS NULL";
        }

        return $unionSql;
    }

    /**
     * Builds the ORDER BY SQL clause based on sorting fields.
     *
     * @return string The ORDER BY SQL clause.
     */
    private function getOrderBySql(): string
    {
        $sortBy = $this->fields['posts_sort_by'] ?? 'date';
        $sortOrder = strtoupper($this->fields['posts_sort_order'] ?? 'DESC');

        // Always prioritize sticky posts
        $orderByParts = ['is_sticky DESC'];

        // Map sortBy to valid SQL columns
        $sortColumns = [
            'date' => 'post_date',
            'title' => 'post_title',
            'modified' => 'post_modified',
            'menu_order' => 'menu_order',
            'rand' => 'RAND()',
        ];

        if ($sortBy === 'rand') {
            $orderByParts[] = $sortColumns['rand'];
        } else {
            $column = $sortColumns[$sortBy] ?? $sortColumns['date'];
            $orderByParts[] = sprintf('%s %s', $column, $sortOrder);
        }

        return ' ORDER BY ' . implode(', ', $orderByParts);
    }

    private function fetchPosts(array $dbResults, array $postStatuses, int $currentBlogId): array
    {
        $dbResults = array_filter($dbResults, fn($item) => !empty($item->post_id));

        $posts = array_map(function ($item) use ($postStatuses, $currentBlogId) {
            $post = $this->wpService->getBlogPost($item->blog_id, $item->post_id);
            if (!$post || !in_array($post->post_status, $postStatuses, true)) {
                return null;
            }
            if ((int)$item->blog_id !== $currentBlogId) {
                $post->originalBlogId = $item->blog_id;
            }
            return $post;
        }, $dbResults);

        return array_values(array_filter($posts));
    }

    private function formatResponse(array $posts, int $maxNumPages, array $stickyPosts): PostsResultInterface
    {
        return new PostsResult($posts, $maxNumPages, $stickyPosts);
    }

    private function getPostsPerPage(): int
    {
        if (isset($this->fields['posts_count']) && is_numeric($this->fields['posts_count'])) {
            $count = (int)$this->fields['posts_count'];
            return ($count == -1 || $count > 100) ? 100 : $count;
        }
        return 10;
    }
}
