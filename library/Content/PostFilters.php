<?php

namespace Municipio\Content;

class PostFilters
{
    public function __construct()
    {
        add_action('wp', array($this, 'initFilters'));

        add_filter('template_include', array($this, 'enablePostTypeArchiveSearch'), 1);

        add_filter('posts_where', array($this, 'doPostDateFiltering'));
        add_filter('pre_get_posts', array($this, 'doPostTaxonomyFiltering'));
        add_filter('pre_get_posts', array($this, 'doPostOrdering'));

        remove_filter('content_save_pre', 'wp_filter_post_kses');
        remove_filter('excerpt_save_pre', 'wp_filter_post_kses');
        remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
    }

    /**
     * Initialize the post filter UI
     * @return void
     */
    public function initFilters()
    {
        global $wp_query;

        if (!get_post_type()) {
            return;
        }

        $queriedObject = get_queried_object();
        $pageForPosts = get_option('page_for_' . get_post_type());

        if (is_null($queriedObject) || ($pageForPosts !== $queriedObject->ID && !is_archive() && !is_post_type_archive() && !is_home()) || is_admin()) {
            return;
        }

        $taxonomies = $this->getEnabledTaxonomies();

        add_action('HbgBlade/data', function ($data) use ($taxonomies) {
            $data['postType'] = get_post_type();

            $data['enabledHeaderFilters'] = get_field('archive_' . get_post_type() . '_post_filters_header', 'option');
            $data['enabledTaxonomyFilters'] = $taxonomies;

            return $data;
        });
    }

    /**
     * Get filterable taxonomies
     * @return array Taxonomies
     */
    public function getEnabledTaxonomies()
    {
        $tax = array();
        $taxonomies = get_field('archive_' . sanitize_title(get_post_type()) . '_post_filters_sidebar', 'option');

        if (!$taxonomies) {
            return array();
        }

        foreach ($taxonomies as $key => $item) {
            $terms = get_terms($item, array(
                'hide_empty' => false
            ));

            $tax[$item] = array(
                'label' => get_taxonomy($item)->labels->name,
                'values' => $terms
            );
        }

        $tax = json_decode(json_encode($tax));
        return $tax;
    }

    /**
     * Use correct template when filtering a post type archive
     * @param  string $template Template path
     * @return string           Template path
     */
    public function enablePostTypeArchiveSearch($template)
    {
        $template = \Municipio\Helper\Template::locateTemplate($template);

        if (is_post_type_archive() && is_search()) {
            $archiveTemplate = \Municipio\Helper\Template::locateTemplate('archive-' . get_post_type() . '.blade.php');

            if (!$archiveTemplate) {
                $archiveTemplate = \Municipio\Helper\Template::locateTemplate('archive.blade.php');
            }

            $template = $archiveTemplate;
        }

        return $template;
    }

    /**
     * Do taxonomy fitering
     * @param  object $query Query object
     * @return object        Modified query
     */
    public function doPostTaxonomyFiltering($query)
    {
        // Do not execute this in admin view
        if (is_admin() || !(is_archive() || is_home()) || !$query->is_main_query()) {
            return $query;
        }

        // Bail if tax or term is empty
        if (!isset($_GET['term']) || empty($_GET['term'])) {
            return $query;
        }

        $terms = (array)$_GET['term'];
        $terms = array_map(function ($item) {
            return explode('|', $item);
        }, $terms);

        $taxQuery = array('relation' => 'OR');
        foreach ($terms as $key => $term) {
            $taxQuery[] = array(
                'taxonomy' => $term[0],
                'field' => 'slug',
                'terms' => $term[1],
                'operator' => 'IN'
            );
        }

        $query->set('tax_query', $taxQuery);

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
        $orderby = (isset($_GET['orderby']) && !empty($_GET['orderby'])) ? sanitize_text_field($_GET['orderby']) : get_field('archive_' . sanitize_title($posttype) . '_sort_key', 'option');
        if (empty($orderby)) {
            $orderby = 'post_date';
        }

        if (in_array($orderby, array('post_date', 'post_modified', 'post_title'))) {
            $orderby = str_replace('post_', '', $orderby);
        } else {
            $isMetaQuery = true;
        }

        // Get orderby order, default to desc
        $order = (isset($_GET['order']) && !empty($_GET['order'])) ? sanitize_text_field($_GET['order']) : get_field('archive_' . sanitize_title($posttype) . '_sort_order', 'option');
        if (empty($order) || !in_array(strtolower($order), array('asc', 'desc'))) {
            $order = 'desc';
        }

        $query->set('order', $order);

        // Return if not meta query
        if (!$isMetaQuery) {
            $query->set('orderby', $orderby);
            return $query;
        }

        if (isset($_GET['orderby']) && $_GET['orderby'] == 'meta_key' && isset($_GET['meta_key']) && !empty($_GET['meta_key'])) {
            $orderby = sanitize_text_field($_GET['meta_key']);
        }

        // Continue if meta query
        $query->set('meta_key', $orderby);
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
