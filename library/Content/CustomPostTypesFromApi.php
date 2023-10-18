<?php

namespace Municipio\Content;

use WP_Post;
use WP_Query;

class CustomPostTypesFromApi
{
    private array $postTypesFromApi = [];
    private array $postTypesWithParentPostTypes = [];

    public function __construct()
    {
        add_action('init', function() {
            $this->postTypesFromApi = $this->getPostTypesFromApi();
            $this->postTypesWithParentPostTypes = $this->getPostTypesWithParentPostTypes();
        }, 10);
    }

    private function getPostTypesFromApi(): array
    {
        $typeDefinitions = CustomPostType::getTypeDefinitions();
        $postTypesFromApi = array_filter(
            $typeDefinitions,
            fn ($typeDefinition) =>
            isset($typeDefinition['api_source_url']) && !empty($typeDefinition['api_source_url'])
        );

        return array_map(fn ($postType) => sanitize_title(substr($postType['post_type_name'], 0, 19)), $postTypesFromApi);
    }

    private function getPostTypesWithParentPostTypes():array {

        $postTypesWithParentPostTypes = [];
        $typeDefinitions = CustomPostType::getTypeDefinitions();
        $matches = array_filter(
            $typeDefinitions,
            fn ($typeDefinition) =>
            isset($typeDefinition['parent_post_types']) && !empty($typeDefinition['parent_post_types'])
        );

        foreach($matches as $match) {
            $postType = sanitize_title(substr($match['post_type_name'], 0, 19));
            $postTypesWithParentPostTypes[$postType] = $match['parent_post_types'];
        }

        return $postTypesWithParentPostTypes;
    }

    public function addHooks(): void
    {
        add_filter('post_type_link', [$this, 'modifyPostTypeLink'], 10, 2);
        add_filter('posts_results', [$this, 'modifyPostsResults'], 10, 2);
        add_filter('default_post_metadata', [$this, 'modifyDefaultPostMetadata'], 10, 5);

        add_filter('Municipio/Breadcrumbs/Items', [$this, 'modifyBreadcrumbsItems'], 10, 3);
        add_filter('Municipio/Content/RestApiPostToWpPost', [$this, 'modifySchoolPage'], 10, 3);
        // add_filter('Municipio/Controller/Archive/Data', [$this, 'modifyArchiveData'], 10, 1);

        add_action('pre_get_posts', [$this, 'preventSuppressFiltersOnWpQuery'], 10, 1);
        add_action('pre_get_posts', [$this, 'preventCacheOnPreGetPosts'], 10, 1);
        add_action('init', [$this, 'addRewriteRulesForPostTypesWithParentPostTypes'], 10, 0);
    }

    public function modifyPostTypeLink(string $postLink, WP_Post $post)
    {
        $postType = get_post_type($post);

        if (isset($this->postTypesWithParentPostTypes[$postType])) {

            $parentPost = get_post($post->post_parent);
            $parentPostType = $parentPost->post_type;

            if (in_array($parentPostType, $this->postTypesWithParentPostTypes[$postType])) {
                $parentPostTypeObject = get_post_type_object($parentPostType);
                $postTypeObject = get_post_type_object($postType);
                $rewriteSlug = $postTypeObject->rewrite['slug'];
                $parentRewriteSlug = $parentPostTypeObject->rewrite['slug'];
                $postLink = str_replace($rewriteSlug, $parentRewriteSlug, $postLink);
            }
        }

        return $postLink;
    }

    public function modifyBreadcrumbsItems(array $pageData, $queriedObject, $queriedObjectData): array
    {
        if (!is_a($queriedObject, 'WP_Post')) {
            return $pageData;
        }

        // if post type in entity registry
        if (!isset($this->postTypesWithParentPostTypes[$queriedObject->post_type])) {
            return $pageData;
        }

        if (empty($queriedObject->post_parent)) {
            return $pageData;
        }

        foreach($this->postTypesWithParentPostTypes[$queriedObject->post_type] as $parentPostType) {

            $parentPosts = get_posts(['post__in' => [$queriedObject->post_parent], 'post_type' => $parentPostType, 'suppress_filters' => false]);

            if( !empty($parentPosts) ) {
                break;
            }
        }

        if( empty($parentPosts) ) {
            return $pageData;
        }

        // Insert new element before the last one in $pageData.
        array_splice($pageData, -1, 0, [
            [
                'label'   => $parentPosts[0]->post_title,
                'href'    => get_post_permalink($parentPosts[0]),
                'current' => false
            ],
        ]);

        return $pageData;
    }

    public function modifyPostsResults(array $posts, WP_Query $query): array
    {
        if (
            !$query->get('post_type') ||
            !in_array($query->get('post_type'), $this->postTypesFromApi)
        ) {
            return $posts;
        }

        $posts = $query->is_single()
            ? CustomPostTypeFromApi::getSingle($query->get('name'), $query->get('post_type'))
            : CustomPostTypeFromApi::getCollection($query, $query->get('post_type'));

        return is_array($posts) ? $posts : [$posts];
    }

    public function modifyDefaultPostMetadata($value, $objectId, $metaKey, $single, $metaType)
    {
        $postType = get_post_type($objectId);

        if (!in_array($postType, $this->postTypesFromApi)) {
            return $value;
        }

        return CustomPostTypeFromApi::getMeta($objectId, $metaKey, $single, $metaType, $postType) ?? $value;
    }

    public function modifySchoolPage(WP_Post $wpPost, object $restApiPost, string $postType)
    {
        if ($postType === 'school-page') {
            $wpPost->post_parent = $restApiPost->acf->parent_school ?? 0;
        }
        
        return $wpPost;
    }

    public function modifyArchiveData(array $data): array
    {
        if (!$this->shouldApplyModifier($data)) {
            return $data;
        }

        $preparedPosts = [
            'items'    => [],
            'headings' => [
                __('Förskola', ASI_TEXT_DOMAIN),
                __('Område', ASI_TEXT_DOMAIN),
            ]
        ];

        if (is_array($data['posts']['items']) && !empty($data['posts']['items'])) {
            foreach ($data['posts']['items'] as $post) {
                $areaTerms = wp_get_post_terms($post['id'], 'area');
                $areaName  = !empty($areaTerms) ? reset($areaTerms)->name : '';

                $title = sprintf(
                    '<a href="%s" title="%s">%s</a>',
                    $post['href'],
                    $post['columns'][0],
                    $post['columns'][0]
                );

                $preparedPosts['items'][] = [
                    'columns' => [
                        $title,
                        $areaName
                    ]
                ];
            }

            //Assign as list
            $data['posts'] = $preparedPosts;
        }

        return $data;
    }

    public function preventSuppressFiltersOnWpQuery(WP_Query $query): void
    {
        if (!in_array($query->get('post_type'), $this->postTypesFromApi)) {
            return;
        }

        $query->query['suppress_filters'] = false;
    }

    public function preventCacheOnPreGetPosts(WP_Query $query): void
    {
        if (!in_array($query->get('post_type'), $this->postTypesFromApi)) {
            return;
        }

        $query->set('update_post_meta_cache', false);
        $query->set('update_post_term_cache', false);
    }

    public function addRewriteRulesForPostTypesWithParentPostTypes(): void
    {
        foreach ($this->postTypesWithParentPostTypes as $postType => $parentPostTypes) {

            if (!post_type_exists($postType)) {
                return;
            }

            foreach ($parentPostTypes as $parentPostType) {

                if (!post_type_exists($parentPostType)) {
                    continue;
                }

                $parentPostTypeObject = get_post_type_object($parentPostType);
                $rewriteSlug = $parentPostTypeObject->rewrite['slug'];

                add_rewrite_rule(
                    $rewriteSlug . '/(.*)/(.*)',
                    'index.php?post_type=' . $postType . '&name=$matches[2]',
                    'top'
                );
            }
        }
    }
}
