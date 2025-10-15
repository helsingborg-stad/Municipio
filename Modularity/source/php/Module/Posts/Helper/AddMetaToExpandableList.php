<?php

namespace Modularity\Module\Posts\Helper;

/**
 * Class File
 * @package Modularity\Module\Posts\Helper
 */
class AddMetaToExpandableList
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'addColumnFields'));
        add_action('save_post', array($this, 'saveColumnFields'));
    }

       /* Handle Expandable list post meta fields */
            /**
     * Saves column names if exandable list template is used
     * @param int $postId The id of the post
     * @return void
     */
    public function saveColumnFields($postId)
    {
        //Meta key
        $metaKey = "modularity-mod-posts-expandable-list";

        //Bail early if autosave, cron or post request
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_CRON') && DOING_CRON) ||
        !isset($_POST) || (is_array($_POST) && empty($_POST)) || !is_array($_POST)) {
            return false;
        }

        //Update if nonce verification succeed
        if (
            isset($_POST['modularity_post_columns'])
            && wp_verify_nonce(
                $_POST['modularity_post_columns'],
                'save_columns'
            )
        ) {
            //Delete if not posted data
            if (!isset($_POST[$metaKey])) {
                delete_post_meta($postId, $metaKey);
                return;
            }

            //Save meta data
            update_post_meta($postId, $metaKey, $_POST[$metaKey]);
        }
    }

    public function addModules($postId) {
        $modules = [];
        $modules = array_merge($modules, !empty($this->checkIfManuallyPicked($postId)) ? $this->checkIfManuallyPicked($postId) : []);
        $modules = array_merge($modules, !empty($this->checkIfPostType($postId)) ? $this->checkIfPostType($postId) : []);
        $modules = array_merge($modules, !empty($this->checkIfChild($postId)) ? $this->checkIfChild($postId) : []);

        return $modules;
    }

    /**
     * Check wheather to add expandable list column fields to edit post screeen
     */
    public function addColumnFields()
    {
        global $post;
        $screen = get_current_screen();
        
        if (empty($post->post_type) || $screen->base != 'post') {
            return;
        }

        $modules = $this->addModules($post->ID);

        if (empty($modules)) {
            return false;
        }

        $modules = array_filter($modules, function ($item) {
            return !wp_is_post_revision($item);
        });

        $fields = $this->getColumns($modules);

        if (!empty($fields)) {
            add_meta_box(
                'modularity-mod-posts-expandable-list',
                __('Modularity expandable list column values', 'modularity'),
                [$this, 'columnFieldsMetaBoxContent'],
                null,
                'normal',
                'default',
                [$fields]
            );
        }
    }

    /**
     * Expandable list column value fields metabox content
     * @param object $post Post object
     * @param array $args Arguments
     * @return void
     */
    public function columnFieldsMetaBoxContent($post, $args)
    {
        $fields = $args['args'][0];
        $fieldValues = get_post_meta($post->ID, 'modularity-mod-posts-expandable-list', true);

        foreach ($fields as $field) {
            $fieldSlug = sanitize_title($field);
            $value = isset($fieldValues[$fieldSlug]) && !empty($fieldValues[$fieldSlug])
                ? $fieldValues[$fieldSlug] : '';
            echo '
                <p>
                    <label for="mod-' . $fieldSlug . '">' . $field . ':</label>
                    <input value="' . $value . '" class="widefat" type="text" name="modularity-mod-posts-expandable-list[' . sanitize_title($field) . ']" id="mod-' . sanitize_title($field) . '">
                </p>
            ';
        }

        echo wp_nonce_field('save_columns', 'modularity_post_columns');
    }

        /**
     * Get field columns
     * @param array $posts Post ids
     * @return array        Column names
     */
    public function getColumns($posts)
    {
        $columns = [];

        if (is_array($posts)) {
            foreach ($posts as $post) {
                $values = get_field('posts_list_column_titles', $post);

                if (is_array($values)) {
                    foreach ($values as $value) {
                        $columns[] = $value['column_header'];
                    }
                }
            }
        }

        return $columns;
    }

        /**
     * Check if current post is included in a manually picked data source in exapndable list
     * @param integer $id Post id
     * @return array       Modules included in
     */
    public function checkIfManuallyPicked($id)
    {
        global $wpdb;

        $result = $wpdb->get_results("
            SELECT *
            FROM $wpdb->postmeta
            WHERE meta_key = 'posts_data_posts'
                  AND meta_value LIKE '%\"{$id}\"%'
        ", OBJECT);

        if (count($result) === 0) {
            return false;
        }

        $posts = [];
        foreach ($result as $item) {
            $posts[] = $item->post_id;
        }

        return $posts;
    }

     /**
     * Check if current post is included in the data source post type
     * @param integer $id Postid
     * @return array       Modules included in
     */
    public function checkIfPostType($id)
    {
        global $post;
        global $wpdb;

        $result = $wpdb->get_results("
            SELECT *
            FROM $wpdb->postmeta
            WHERE meta_key = 'posts_data_post_type'
                  AND meta_value = '{$post->post_type}'
        ", OBJECT);

        if (count($result) === 0) {
            return false;
        }
        
        $posts = [];
        foreach ($result as $item) {
            $posts[] = $item->post_id;
        }
        return $posts;
    }

    public function checkIfChild($id)
    {
        global $post;
        global $wpdb;

        $result = $wpdb->get_results("
            SELECT *
            FROM $wpdb->postmeta
            WHERE meta_key = 'posts_data_child_of'
                  AND meta_value = '{$post->post_parent}'
        ", OBJECT);

        if (count($result) === 0) {
            return false;
        }

        $posts = [];
        foreach ($result as $item) {
            $posts[] = $item->post_id;
        }

        return $posts;
    }
}
