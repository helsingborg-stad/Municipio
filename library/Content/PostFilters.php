<?php

namespace Municipio\Content;

class PostFilters
{
    public function __construct()
    {
        add_action('wp', array($this, 'initFilters'));

        add_filter('template_include', array($this, 'enablePostTypeArchiveSearch'), 1);
        add_filter('query_vars', array($this, 'addQueryVars'));

        add_action('posts_where', array($this, 'doPostDateFiltering'));
        add_action('pre_get_posts', array($this, 'doPostTaxonomyFiltering'));
        add_action('pre_get_posts', array($this, 'doPostOrderBy'));
        add_action('pre_get_posts', array($this, 'doPostOrderDirection'));

        remove_filter('content_save_pre', 'wp_filter_post_kses');
        remove_filter('excerpt_save_pre', 'wp_filter_post_kses');
        remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
    }

    public function addQueryVars($vars) {
        if (!is_array($vars)) {
            $vars = [];
        }

        $vars[] = "orderby";
        $vars[] = "order";

        return $vars;
    }

    /**
     * Get post type
     * @return string
     */
    public function getPostType()
    {
        global $wp_query;

        // If taxonomy or category page and post type not isset then it's the "post" post type
        if (is_home() || ((is_tax() || is_category() || is_tag()) && is_a(get_queried_object(),
                    'WP_Term') && !get_post_type())) {
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

        //Only run on frontend
        if (is_admin()) {
            return;
        }

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

        add_action('Municipio/viewData', function ($data) use ($wp_query, $postType) {

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

            $data['searchQuery'] = $this->getSearchQuery();

            return $data;
        });
    }

    /**
     * Returns escaped search query
     * @return string Search query
     */
    public function getSearchQuery()
    {
        $searchQuery = '';
        if (!empty(get_search_query())) {
            $searchQuery = get_search_query();
        } elseif (!empty($_GET['s'])) {
            $searchQuery = esc_attr($_GET['s']);
        }

        return $searchQuery;
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

    /**
     * Trying to sort terms natural
     * @param $terms
     * @return array
     */
    public static function sortTerms($terms)
    {
        $sort_terms = array();
        foreach ($terms as $term) {
            $sort_terms[$term->name] = $term;
        }
        uksort($sort_terms, 'strnatcmp');

        return $sort_terms;
    }


    public static function getMultiTaxDropdown($tax, int $parent = 0, string $class = '')
    {
        $termArgs = array(
            'hide_empty' => false,
            'parent' => $parent
        );

        $terms = get_terms($tax->slug, $termArgs);
        $terms = self::sortTerms($terms);

        $inputType = $tax->type === 'single' ? 'radio' : 'checkbox';

        $html = '<ul';

        if (!empty($class)) {
            $html .= ' class="' . $class . '"';
        }

        $html .= '>';

        foreach ($terms as $term) {
            $isChecked = isset($_GET['filter'][$tax->slug]) && ($_GET['filter'][$tax->slug] === $term->slug || in_array($term->slug,
                        $_GET['filter'][$tax->slug]));
            $checked = checked(true, $isChecked, false);

            $html .= '<li>';
            $html .= '<label class="checkbox">';
            $html .= '<input type="' . $inputType . '" name="filter[' . $tax->slug . '][]" value="' . $term->slug . '" ' . $checked . '> ' . $term->name;
            $html .= '</label>';

            $html .= self::getMultiTaxDropdown($tax, $term->term_id);
            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Get filter options as list (refined getMultiTaxDropdown())
     * @return string unordered list of terms as checkbox/radio
     */
    public static function getFilterOptionsByTax($tax, int $parent = 0, string $class = '')
    {
        $termArgs = array(
            'hide_empty' => false,
            'parent' => $parent
        );

        $terms = get_terms($tax->slug, $termArgs);

        if (!isset($terms) || !is_array($terms) || empty($terms)) {
            return;
        }

        $inputType = $tax->type === 'single' ? 'radio' : 'checkbox';

        $html = '<ul';

        if (!empty($class)) {
            $html .= ' class="' . $class . '"';
        }

        $html .= '>';

        foreach ($terms as $term) {
            $isChecked = isset($_GET['filter'][$tax->slug]) && ($_GET['filter'][$tax->slug] === $term->slug || in_array($term->slug,
                        $_GET['filter'][$tax->slug]));
            $checked = checked(true, $isChecked, false);

            $html .= '<li>';
            $html .= '<input id="filter-option-' . $term->slug . '" type="' . $inputType . '" name="filter[' . $tax->slug . '][]" value="' . $term->slug . '" ' . $checked . '>';
            $html .= '<label for="filter-option-' . $term->slug . '" class="checkbox">';
            $html .= $term->name;
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

            $placement = get_field('archive_' . sanitize_title($postType) . '_filter_' . sanitize_title($item) . '_placement',
                'option');
            if (is_null($placement)) {
                $placement = 'secondary';
            }

            $type = get_field('archive_' . sanitize_title($postType) . '_filter_' . sanitize_title($item) . '_type',
                'option');

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

        $taxQuery = array('relation' => 'OR');

        foreach ($filterable as $key => $value) {

            if (!isset($_GET[$key]) || empty($_GET[$key]) || $_GET[$key] === '-1') {
                continue;
            }
            
            $terms = $_GET[$key];
            
            $taxQuery[] = array(
                'taxonomy' => $key,
                'field' => 'slug',
                'terms' => $terms,
                'operator' => 'AND'
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
                        'terms' => (array)get_queried_object()->slug,
                        'operator' => 'IN'
                        )
                    ),
                    $taxQuery
                );
            }
            
            $taxQuery = apply_filters('Municipio/archive/tax_query', $taxQuery, $query);
            
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
        //Only run on frontend
        if (is_admin()) {
            return $where;
        }

        global $wpdb;

        $from = null;
        $to = null;

        if (isset($_GET['from']) && !empty($_GET['from'])) {
            $from = sanitize_text_field($_GET['from']);
            $from = date('Y-m-d', \strtotime(str_replace('/', '-', $from)));
        }

        if (isset($_GET['to']) && !empty($_GET['to'])) {
            $to = sanitize_text_field($_GET['to']);
            $to = date('Y-m-d', \strtotime(str_replace('/', '-', $to)));
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
     * Determines if order shuld be ASC or DESC.
     *
     * @param WP_Query $query
     * @return WP_Query
     */
    public function doPostOrderDirection($query)
    {
        if (!$this->shouldFilter($query)) {
            return $query;
        }

        if (!$order = $this->getQueryString('order')) {
            $order = get_theme_mod(
                'archive_' . $this->getCurrentPostType($query) . '_order_direction',
                'desc'
            );
        }

        if (!in_array(strtolower($order), array('asc', 'desc'))) {
            $order = 'desc';
        }

        $query->set('order', $order);

        return $query;
    }

    /**
     * Do post orderBy for archives
     * @param  object $query Query
     * @return object        Modified query
     */
    public function doPostOrderBy($query)
    {
        if (!$this->shouldFilter($query)) {
            return $query;
        }

        $postType = $this->getCurrentPostType($query);

        if (!$orderBy = $this->getQueryString('orderby')) {
            $orderBy = get_theme_mod('archive_' . $postType . '_order_by', 'post_date');
        }

        if (!$this->isMetaQuery($orderBy)) {
            $query->set(
                'orderby',
                str_replace('post_', '', $orderBy)
            );
        } elseif ($orderBy == 'meta_key') {
            if ($orderBy = $this->getQueryString($orderBy, false)) {
                $query->set('meta_key', $orderBy);
                $query->set('orderby', 'meta_value');
            }
        }

        return $query;
    }

    /**
     * Get current post type
     * @param  object $query Query object
     * @return string        Post type
     */
    private function getCurrentPostType($query)
    {
        if ($postType = sanitize_title($query->get('post_type'))) {
            return $postType;
        }
        return 'post';
    }

    /**
     * Get a query string
     * @param  string   $queryString    Qs name
     * @return boolean  $known          Use the wordpress-way (known), or php-way (unknown).
     */
    private function getQueryString($queryString, $known = true)
    {
        if ($known === true) {
            return get_query_var($queryString, false);
        }

        if (isset($_GET[$queryString]) && !empty($_GET[$queryString])) {
            return sanitize_text_field($_GET[$queryString]);
        }

        return false;
    }

    /**
     * Do the value match any standard keys in posts table?
     * Then it's not a meta_query
     *
     * @param string $key
     * @return boolean
     */
    private function isMetaQuery($key)
    {
        return in_array($key, array('post_date', 'post_modified', 'post_title'));
    }

    /**
     * Determine if filter should be used here.
     *
     * @param WP_Query $query
     * @return boolean
     */
    private function shouldFilter($query) {
        if (is_admin() || !(is_archive() || is_home()) || !$query->is_main_query()) {
            return false;
        }
        return true;
    }
}
