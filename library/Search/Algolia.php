<?php

namespace Municipio\Search;

class Algolia
{
    public function __construct()
    {

        //Algolia search modifications
        add_filter('algolia_should_index_searchable_post', array($this, 'shouldIndexPost'), 10, 2);

        //Do not run if not enabled
        if (!get_field('use_algolia_search', 'option')) {
            return false;
        }

        //Exclude from search UI
        add_action('post_submitbox_misc_actions', array($this, 'excludeFromSearchCheckbox'), 100);
        add_action('attachment_submitbox_misc_actions', array($this, 'excludeFromSearchCheckbox'), 100);
        add_action('save_post', array($this, 'saveExcludeFromSearch'));
        add_action('edit_attachment', array($this, 'saveExcludeFromSearch'));
    }

    /**
     * Adds form field for exclude from search
     * @return void
     */

    public function excludeFromSearchCheckbox()
    {
        global $post;

        //Only show if not set to not index
        if (!$this->shouldIndexPost(true, $post, false)) {
            return false;
        }

        if (is_object($post) && isset($post->ID)) {
            $checked = checked(true, get_post_meta($post->ID, 'exclude_from_search', true), false);
            echo '
            <div class="misc-pub-section">
                <label><input type="checkbox" name="algolia-exclude-from-search" value="true" ' . $checked . '> ' . __('Exclude from search', 'municipio') . '</label>
            </div>
            ';
        }
    }

    /**
     * Saves the "exclude from search" value
     * @param  int $postId The post id
     * @return void
     */

    public function saveExcludeFromSearch($postId)
    {
        if (!isset($_POST['algolia-exclude-from-search']) || $_POST['algolia-exclude-from-search'] != 'true') {
            delete_post_meta($postId, 'exclude_from_search');
            return;
        }
        update_post_meta($postId, 'exclude_from_search', true);
    }

    /**
     * Remove post types from index that are hidden for the user
     * @param bool $should_index The decition originally done by algolia
     * @param WpPost $post The post that should be indexed or not
     * @param bool $includeCheckbox If the check should take the users decition in consideration
     * @return bool True if add, false if not indexable
     */

    public function shouldIndexPost($should_index, $post, $includeCheckbox = true)
    {

        //Do not index if excluded
        if ($includeCheckbox && isset($post->ID) && get_post_meta($post->ID, 'exclude_from_search', true) == "1") {
            return false;
        }

        //Get post type object
        if (isset($post->post_type)) {

            //Default to index pages (discard other stuff below)
            $defaultIndexableTypes = apply_filters('algolia_indexable_post_types', array(
                'page'
            ));
            if (in_array($post->post_type, $defaultIndexableTypes)) {
                return true;
            }
        }

        //Get post type object
        if (isset($post->post_type)) {

            //Get post object details
            $postTypeObject = get_post_type_object($post->post_type);

            //Do not index post that are not searchable
            if (isset($postTypeObject->exclude_from_search)) {
                if ($postTypeObject->exclude_from_search) {
                    return false;
                }
            }

            //Do not index posts that are not public
            if (isset($postTypeObject->publicly_queryable)) {
                if ($postTypeObject->publicly_queryable === false) {
                    return false;
                }
            }
        }

        //Attachments
        if (isset($post->post_type) && $post->post_type == 'attachment') {
            $indexable_mimes = apply_filters('algolia_indexable_attachment_mime_types', array(
                'application/pdf'
            ));

            if (!in_array(get_post_mime_type($post->ID), $indexable_mimes)) {
                return false;
            }
        }

        return $should_index;
    }
}
