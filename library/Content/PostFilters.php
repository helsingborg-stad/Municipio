<?php

namespace Municipio\Content;

class PostFilters
{
    public function __construct()
    {
        add_filter('template_include', array($this, 'enablePostTypeArchiveSearch'), 1);

        add_filter('posts_where', array($this, 'doPostDateFiltering'));
        add_filter('pre_get_posts', array($this, 'doPostTaxonomyFiltering'));
        add_filter('pre_get_posts', array($this, 'doPostOrdering'));
    }

    public function enablePostTypeArchiveSearch($template)
    {
        $template = \Municipio\Helper\Template::locateTemplate($template);

        if (!$template && is_post_type_archive() && is_search()) {
            $template = \Municipio\Helper\Template::locateTemplate('archive.blade.php');
        }

        return $template;
    }

    public function doPostTaxonomyFiltering($query)
    {
        // Do not execute this in admin view
        if (is_admin() || !(is_archive() || is_home()) || !$query->is_main_query()) {
            return $query;
        }

        // Bail if tax or term is empty
        if (!isset($_GET['tax']) || empty($_GET['tax']) || !isset($_GET['term']) || empty($_GET['term'])) {
            return $query;
        }

        $tax = sanitize_text_field($_GET['tax']);
        $term = sanitize_text_field($_GET['term']);

        $query->set('tax_query', array(
            'relation' => 'OR',
            array(
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => $term,
                'operator' => 'IN'
            )
        ));

        return $query;
    }

    /**
     * Add where clause to post query based on active filters
     * @param  string $where Original where clause
     * @return string        Modified where clause
     */
    public function doPostDateFiltering($where)
    {
        global $wpdb;

        $from = null;
        $to = null;

        if (isset($_GET['from']) && !empty($_GET['from'])) {
            $from = sanitize_text_field($_GET['from']);
        }

        if (isset($_GET['to']) && !empty($_GET['to'])) {
            $to = sanitize_text_field($_GET['to']);
        }

        if (!is_null($from) && !is_null($to)) {
            $where .= " AND ($wpdb->posts.post_date >= '$from' AND $wpdb->posts.post_date <= '$to')";
        } elseif (!is_null($from) && is_null($to)) {
            $where .= " AND ($wpdb->posts.post_date >= '$from')";
        } elseif (is_null($from) && !is_null($to)) {
            $where .= " AND ($wpdb->posts.post_date <= '$to')";
        }

        $where = apply_filters('Municipio/archive/date_filter', $where, $from, $to);

        return $where;
    }

    /**
     * Do post ordering for archives
     * @param  object $query Query
     * @return object        Modified query
     */
    public function doPostOrdering($query)
    {
        // Do not execute this in admin view
        if (is_admin() || !(is_archive() || is_home()) || !$query->is_main_query()) {
            return $query;
        }

        $isMetaQuery = false;

        $posttype = $query->get('post_type');
        if (empty($posttype)) {
            $posttype = 'post';
        }

        // Get orderby key, default to post_date
        $orderby = get_field('archive_' . sanitize_title($posttype) . '_sort_key', 'option');
        if (empty($orderby)) {
            $orderby = 'post_date';
        }

        if (in_array($orderby, array('post_date', 'post_modified', 'post_title'))) {
            $orderby = str_replace('post_', '', $orderby);
        } else {
            $isMetaQuery = true;
        }

        // Get orderby order, default to desc
        $order = get_field('archive_' . sanitize_title($posttype) . '_sort_order', 'option');
        if (empty($order)) {
            $order = 'desc';
        }

        $query->set('order', $order);

        // Return if not meta query
        if (!$isMetaQuery) {
            $query->set('orderby', $orderby);

            return $query;
        }

        // Continue if meta query
        $query->set(
            'meta_query',
            array(
                'relation' => 'OR',
                array(
                    'key' => $orderby,
                    'compare' => 'EXISTS'
                ),
                array(
                    'key' => $orderby,
                    'compare' => 'NOT EXISTS'
                )
            )
        );

        $query->set('orderby', 'meta_key');

        return $query;
    }
}
