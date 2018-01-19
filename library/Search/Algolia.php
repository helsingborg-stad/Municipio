<?php

namespace Municipio\Search;

class Algolia
{
    public function __construct()
    {
        //Do not run if not enabled
        if (!get_field('use_algolia_search', 'option')) {
            return false;
        }

        //Algolia search modifications
        add_filter('algolia_should_index_searchable_post', array($this, 'shouldIndexPost'), 10, 2);
    }


    /**
     * Remove post types from index that are hidden for the user
     * @param $post The post that should be indexed or not
     * @return bool True if add, false if not indexable
     */

    public function shouldIndexPost($should_index, $post)
    {

        //Default value
        if (is_null($should_index)) {
            $should_index = true;
        }

        //Get post type object
        if (isset($post->post_type) && $postTypeObject == get_post_type_object($post->post_type)) {

            //Do not index post that are not searchable
            if (isset($postTypeObject->exclude_from_search)) {
                if ($postTypeObject->exclude_from_search) {
                    return false;
                }
            }

            //Do not index posts that are not public
            if (isset($postTypeObject->publicly_queryable)) {
                if (empty($postTypeObject->publicly_queryable)) {
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
