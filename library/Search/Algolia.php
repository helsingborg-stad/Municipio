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
        add_filter('algolia_should_index_post', array($this, 'shouldIndexPost'));
    }


    /**
     * Remove post types from index that are hidden for the user
     * @param $post The post that should be indexed or not
     * @return bool True if add, false if not indexable
     */

    public function shouldIndexPost($post)
    {
        //Get post type object
        if (isset($post->post_type) && $postTypeObject = get_post_type_object($post->post_type)) {

            //Do not index post that are not searchable
            if ($postTypeObject->exclude_from_search) {
                return false;
            }

            //Do not index posts that are not public
            if (!$postTypeObject->publicly_queryable) {
                return false;
            }
        }
        return true;
    }
}
