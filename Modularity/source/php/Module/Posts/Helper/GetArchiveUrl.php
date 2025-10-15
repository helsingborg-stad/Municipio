<?php

namespace Modularity\Module\Posts\Helper;

class GetArchiveUrl
{
        /**
     * Get the archive URL for a specified post type using provided fields.
     *
     * This function retrieves the archive URL for a specified post type based on the given fields.
     * If the post type is empty or if the archive link field is not set or falsy, it returns false.
     * If the post type is "post," it attempts to retrieve the posts archive URL.
     * Otherwise, it attempts to retrieve the archive URL for the custom post type.
     *
     * @param string      $postType The name of the post type.
     * @param object|null $fields   An object containing fields related to the post type.
     *
     * @return string|false The archive URL if it exists, or false if it doesn't.
     */
    public function getArchiveUrl($postType, $fields) {

        if(is_array($fields)) {
            $fields = (object) $fields;
        }

        if (empty($postType) || !isset($fields->archive_link) || !$fields->archive_link) {
            return false;
        }

        if ($postType == 'post' && $archiveUrl = $this->getPostsArchiveUrl()) {
            return $archiveUrl;
        }

        if($archiveUrl = $this->getPostTypeArchiveUrl($postType)) {
            return $archiveUrl;
        }

        return false;
    }

    /**
     * Get the archive URL for the posts page.
     *
     * This function retrieves the URL of the page that displays the blog posts archive.
     * If a static page is set as the posts page, it returns the permalink to that page.
     * If the option "Front page displays" is set to "Your latest posts," it returns the home URL.
     * If no valid posts page is found, it returns false.
     *
     * @return string|false The archive URL if it exists, or false if it doesn't.
     */
    private function getPostsArchiveUrl() {
        $pageForPosts = get_option('page_for_posts');

        if(is_numeric($pageForPosts) && get_post_status($pageForPosts) == 'publish') {
            return get_permalink($pageForPosts); 
        }

        if(get_option('show_on_front') == 'posts') {
            return get_home_url(); 
        }

        return false;
    }

    /**
     * Get the archive URL for a custom post type.
     *
     * This function retrieves the archive URL for a given custom post type.
     * If the post type does not have an archive, it returns false.
     *
     * @param string $postType The key of the custom post type.
     *
     * @return string|false The archive URL if it exists, or false if it doesn't.
     */
    private function getPostTypeArchiveUrl($postType) {
        if($postTypeObject = get_post_type_object($postType)) {
            if(is_a($postTypeObject, 'WP_Post_Type') && $postTypeObject->has_archive) {
                return get_post_type_archive_link($postType);
            }
        }
        return false;
    }
}
