<?php

namespace Municipio\Content;

class PostFilters
{
    public function __construct()
    {
        add_action('wp', array($this, 'initFilters'));

        add_filter('template_include', array($this, 'enablePostTypeArchiveSearch'), 1);

        add_action('posts_where', array($this, 'doPostDateFiltering'));
        add_action('pre_get_posts', array($this, 'doPostTaxonomyFiltering'));
        add_action('pre_get_posts', array($this, 'doPostOrdering'));

        remove_filter('content_save_pre', 'wp_filter_post_kses');
        remove_filter('excerpt_save_pre', 'wp_filter_post_kses');
        remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
    }

    /**
     * Get post type
     * @return string
     */
    public function getPostType()
    {
        global $wp_query;

        // If taxonomy or category page and post type not isset then it's the "post" post type
        if (is_home() || ((is_tax() || is_category() || is_tag()) && is_a(get_queried_object(), 'WP_Term') && !get_post_type())) {
            return 'post';
        }

        $postType = isset($wp_query->query['post_type']) ? $wp_query->query['post_type'] : false;
        if (!$postType && isset($wp_query->query['category_name']) && !empty($wp_query->query['category_name'])) {
            $postType = 'post';
        }

        if (is_array($postType)) {
            $postType = end($postType);
        }

        return $postType;
    }

    /**
     * Initialize the post filter UI
     * @return void
     */
    public function initFilters()
    {
        global $wp_query;

        if ((is_category() || is_tax() || is_tag()) && !get_post_type()) {
            $postType = 'post';
        }

        $postType = get_post_type();

        if (!$postType && isset($wp_query->query['post_type'])) {
            $postType = $wp_query->query['post_type'];
        }

        if (!$postType) {
            return;
        }

        $queriedObject = get_queried_object();
        $objectId = null;
        if (isset($queriedObject->ID)) {
            $objectId = $queriedObject->ID;
        }

        $pageForPosts = get_option('page_for_' . get_post_type());

        if (($pageForPosts !== $objectId && !is_archive() && !is_post_type_archive() && !is_home() && !is_category() && !is_tax() && !is_tag()) || is_admin()) {
            return;
        }

        add_action('HbgBlade/data', function ($data) use ($wp_query, $postType) {

            //Get current post type enabledTaxonomyFilters
            if (!isset($data['postType']) || !$data['postType']) {
                $data['postType'] = $postType;
            }

            //Get header filters
            if ($enabledHeaderFilters = get_field('archive_' . $data['postType'] . '_post_filters_header', 'option')) {
                $data['enabledHeaderFilters'] = $enabledHeaderFilters;
            } else {
                $data['enabledHeaderFilters'] = array();
            }

            //Get taxonomy filters
            if ($enabledTaxonomyFilters = $this->getEnabledTaxonomies($data['postType'])) {
                $data['enabledTaxonomyFilters'] = $enabledTaxonomyFilters;
            } else {
                $data['enabledTaxonomyFilters'] = array();
            }

            //Is query string present?
            $data['queryString'] = (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) ? true : false;

            //The archive url
            $data['archiveUrl'] = $this->getArchiveSlug($postType);

            return $data;
        });
    }

    /**
     * Get the current archive slug, include category if isset.
     * @param $postType A struing containing av slug of a valid posttype
     * @return string
     */

    public function getArchiveSlug($postType)
    {
        if (is_category()) {
            return get_category_link(get_query_var('cat'));
        }

        return get_post_type_archive_link($postType);
    }

    public static function getMultiTaxDropdown($tax, int $parent = 0, string $class = '')
    {
        $termArgs = array(
            'hide_empty' => false,
            'parent' => $parent
        );

        $terms = get_terms($tax->slug, $termArgs);

        $inputType = $tax->type === 'single' ? 'radio' : 'checkbox';

        $html = '<ul';

        if (!empty($class)) {
            $html .= ' class="' . $class . '"';
        }

        $html .= '>';

        foreach ($terms as $term) {
            $isChecked = isset($_GET['filter'][$tax->slug]) && ($_GET['filter'][$tax->slug] === $term->slug || in_array($term->slug, $_GET['filter'][$tax->slug]));
            $checked = checked(true, $isChecked, false);

            $html .= '<li>';
            $html .= '<label class="checkbox">';
            $html .= '<input type="' . $inputType .'" name="filter[' . $tax->slug . '][]" value="' . $term->slug . '" ' . $checked . '> ' . $term->name;
            $html .= '</label>';

            $html .= self::getMultiTaxDropdown($tax, $term->term_id);
            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Get filterable taxonomies
     * @return array Taxonomies
     */
    public function getEnabledTaxonomies($postType = null, $group = true)
    {
        if (!$postType) {
            $postType = get_post_type();
        }

        $grouped = array();
        $ungrouped = array();
        $taxonomies = get_field('archive_' . sanitize_title($postType) . '_post_filters_sidebar', 'option');

        if (!$taxonomies) {
            return array();
        }

        // Hide category filter if displaying a category
        global $wp_query;
        if (is_category()) {
            $taxonomies = array_filter($taxonomies, function ($item) {
                return $item !== 'category';
            });
        }

        // Hide taxonomy if displaying a taxonomy
        if (is_a(get_queried_object(), 'WP_Term')) {
            $taxonomies = array_diff($taxonomies, (array)get_queried_object()->taxonomy);
        }

        foreach ($taxonomies as $key => $item) {
            $tax = get_taxonomy($item);
            $terms = get_terms($item, array(
                'hide_empty' => false
            ));

            $placement = get_field('archive_' . sanitize_title($postType) . '_filter_' . sanitize_title($item) . '_placement', 'option');
            if (is_null($placement)) {
                $placement = 'secondary';
            }

            $type = get_field('archive_' . sanitize_title($postType) . '_filter_' . sanitize_title($item) . '_type', 'option');

            $grouped[$placement][$tax->name] = array(
                'label' => $tax->label,
                'type' => $type,
                'values' => $terms
            );

            $ungrouped[$tax->name] = array(
                'label' => $tax->label,
                'type' => $type,
                'values' => $terms
            );
        }

        if ($group) {
            $grouped = json_decode(json_encode($grouped));
            return $grouped;
        }

        return $ungrouped;
    }

    /**
     * Use correct template when filtering a post type archive
     * @param  string $template Template path
     * @return string           Template path
     */
    public function enablePostTypeArchiveSearch($template)
    {
        $template = \Municipio\Helper\Template::locateTemplate($template);

        if ((is_post_type_archive() || is_category() || is_date() || is_tax() || is_tag()) && is_search()) {
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
        if (is_admin() || !(is_archive() || is_home() || is_category() || is_tax() || is_tag()) || !$query->is_main_query()) {
            return $query;
        }

        $postType = $this->getPostType();
        $filterable = $this->getEnabledTaxonomies($postType, false);

        if (empty($filterable)) {
            return $query;
        }

        $taxQuery = array('relation' => 'AND');

        foreach ($filterable as $key => $value) {
            if (!isset($_GET['filter'][$key]) || empty($_GET['filter'][$key]) || $_GET['filter'][$key] === '-1') {
                continue;
            }

            $terms = (array) $_GET['filter'][$key];

            $taxQuery[] = array(
                'taxonomy' => $key,
                'field' => 'slug',
                'terms' => $terms,
                'operator' => 'IN'
            );
        }

        if (is_tax() || is_category() || is_tag()) {
            $taxQuery = array(
                'relation' => 'AND',
                array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => get_queried_object()->taxonomy,
                        'field' => 'slug',
                        'terms' => (array) get_queried_object()->slug,
                        'operator' => 'IN'
                    )
                ),
                $taxQuery
            );
        }

        $query->set('tax_query', $taxQuery);
        $query->set('post_type', $this->getPostType());
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
        $query->set('orderby', 'meta_value');

        return $query;
    }
}
