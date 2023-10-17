<?php

namespace Municipio\Content;

use WP_Post;
use WP_Query;
use WP_Term_Query;

class CustomPostTypesFromApi
{
    private array $postTypesFromApi = [];

    public function __construct()
    {
        add_action('init', function() {
            $this->postTypesFromApi = $this->getPostTypesFromApi();
        }, 10);
    }

    private function getPostTypesFromApi(): array
    {
        if (!function_exists('get_field')) {
            return [];
        }

        $typeDefinitions = get_field('avabile_dynamic_post_types', 'option');
        $postTypesFromApi = array_filter(
            $typeDefinitions,
            fn ($typeDefinition) =>
            isset($typeDefinition['api_source_url']) && !empty($typeDefinition['api_source_url'])
        );

        return array_map(fn ($postType) => sanitize_title(substr($postType['post_type_name'], 0, 19)), $postTypesFromApi);
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
        add_action('init', [$this, 'addSchoolPageRewriteRules'], 10, 0);
    }

    public function modifyPostTypeLink(string $postLink, WP_Post $post)
    {
        $postType = get_post_type($post);

        if ($postType === 'school_page') {
            $path     = trim(parse_url(get_post_permalink($post->post_parent))['path'], '/');
            $postLink = str_replace('%school%', $path, $postLink);
        }

        return $postLink;
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

    public function modifyBreadcrumbsItems(array $pageData, $queriedObject, $queriedObjectData): array
    {
        if (!is_a($queriedObject, 'WP_Post')) {
            return $pageData;
        }

        // if post type in entity registry
        if ($queriedObject->post_type !== 'school_page') {
            return $pageData;
        }

        if (empty($queriedObject->post_parent)) {
            return $pageData;
        }

        $schoolPost = CustomPostTypeFromApi::getSingle($queriedObject->post_parent, 'school');

        if (!empty($schoolPost)) {
            // Insert new element before the last one in $pageData.
            array_splice($pageData, -1, 0, [
                [
                    'label'   => $schoolPost->post_title,
                    'href'    => get_post_permalink($schoolPost),
                    'current' => false
                ],
            ]);
        }

        return $pageData;
    }

    public function modifySchoolPage(WP_Post $post, $postType, $restApiPost)
    {
        $post->post_parent = $restApiPost->acf->parent_school ?? 0;
        return $post;
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

    public function addSchoolPageRewriteRules(): void
    {
        if(
            post_type_exists('school_page') &&
            post_type_exists('pre-school') &&
            post_type_exists('elementary-school')
        ) {
            $postType                       = 'school_page';
            $preSchoolPostTypeObject        = get_post_type_object('pre-school');
            $elementarySchoolPostTypeObject = get_post_type_object('elementary-school');
    
            $preSchoolRewriteSlug        = $preSchoolPostTypeObject->rewrite['slug'];
            $elementarySchoolRewriteSlug = $elementarySchoolPostTypeObject->rewrite['slug'];
    
            add_rewrite_rule(
                $preSchoolRewriteSlug . '/(.*)/(.*)',
                'index.php?post_type=' . $postType . '&name=$matches[2]',
                'top'
            );
    
            add_rewrite_rule(
                $elementarySchoolRewriteSlug . '/(.*)/(.*)',
                'index.php?post_type=' . $postType . '&name=$matches[2]',
                'top'
            );
        }
    }
}
