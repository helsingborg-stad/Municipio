<?php

namespace Modularity\Module\Posts;

class PostsAjax {
    /**
     * @var Posts
     */
    private $posts;

    public function __construct(Posts $posts) {
        $this->posts = $posts;

        add_action('wp_ajax_get_taxonomy_types_v2', array($this, 'getTaxonomyTypes'));
        add_action('wp_ajax_get_taxonomy_values_v2', array($this, 'getTaxonomyValues'));
        add_action('wp_ajax_mod_posts_get_date_source', array($this, 'loadDateFieldAjax'));
        //TODO: Is wp_ajax_get_sortable_meta_keys_v2 ever used?
        add_action('wp_ajax_get_sortable_meta_keys_v2', array($this, 'getSortableMetaKeys'));
    }

    public function loadDateFieldAjax()
    {
        $postType = $_POST['state'] ?? false;

        if (empty($postType)) {
            return false;
        }

        wp_send_json($this->posts->getDateSource($postType));
    }

        /**
     * AJAX CALLBACK
     * Get availabel taxonomies for a post type
     * @return void
     */
    public function getTaxonomyTypes()
    {
        if (!isset($_POST['posttype']) || empty($_POST['posttype'])) {
            echo '0';
            die();
        }

        $post = $_POST['post'];

        $result = [
            'types' => get_object_taxonomies($_POST['posttype'], 'objects'),
            'curr' => get_field('posts_taxonomy_type', $post)
        ];

        echo json_encode($result);
        die();
    }

        /**
     * AJAX CALLBACK
     * Gets a taxonomies available values
     * @return void
     */
    public function getTaxonomyValues()
    {
        if (!isset($_POST['tax']) || empty($_POST['tax'])) {
            echo '0';
            die();
        }

        $taxonomy = $_POST['tax'];
        $post = $_POST['post'];

        $result = [
            'tax' => get_terms($taxonomy, [
                'hide_empty' => false,
            ]),
            'curr' => get_field('posts_taxonomy_value', $post)
        ];

        echo json_encode($result);
        die();
    }

    /**
     * AJAX CALLBACK
     * Get metakeys which we can use to sort the posts
     * @return void
     */
    public function getSortableMetaKeys()
    {
        if (!isset($_POST['posttype']) || empty($_POST['posttype'])) {
            echo '0';
            die();
        }

        $meta = \Municipio\Helper\Post::getPosttypeMetaKeys($_POST['posttype']);

        $response = [
            'meta_keys' => $meta,
            'sort_curr' => get_field('posts_sort_by', $_POST['post']),
            'filter_curr' => get_field('posts_meta_key', $_POST['post']),
        ];

        echo json_encode($response);
        die();
    }
}
