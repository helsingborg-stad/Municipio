<?php

namespace Municipio\Content;

/**
 * Class PostFilters
 *
 * Handles various post filters and queries for the Municipio theme.
 */
class PostFilters
{
    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('pre_get_posts', array($this, 'suppressFiltersOnFontAttachments'));

        add_action('parse_query', array($this, 'handleQuery'));

        remove_filter('content_save_pre', 'wp_filter_post_kses');
        remove_filter('excerpt_save_pre', 'wp_filter_post_kses');
        remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
    }

    /**
     * Handle query vars before pre_get_posts
     *
     * @param  object $query Query object
     */
    public function handleQuery($query)
    {
        if (is_post_type_archive()) {
            $query->is_tax = false;
        }
    }

    /**
     * Suppress filters on font attachments
     */
    public function suppressFiltersOnFontAttachments($query)
    {
        /**
         * Suppress filters for font attachments in queries
         *
         * @param WP_Query $query
         * @return void
         */
        if (
            $query->get('post_type') == 'attachment' && is_array($query->get('post_mime_type')) &&
                !empty(array_filter($query->get('post_mime_type'), function ($item) {
                    return strpos($item, 'font') !== false;
                }))
        ) {
                $query->set('suppress_filters', true);
        }

        return $query;
    }
}
