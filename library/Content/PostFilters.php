<?php

namespace Municipio\Content;

class PostFilters
{
    public function __construct()
    {
        add_filter('template_include', array($this, 'enablePostTypeArchiveSearch'), 1);

        add_filter('query_vars', array($this, 'addQueryVars'));

        add_action('posts_where', array($this, 'doPostDateFiltering'));

        add_action('pre_get_posts', array($this, 'suppressFiltersOnFontAttachments'));

        add_action('pre_get_posts', array($this, 'doPostTaxonomyFiltering'));
        add_action('pre_get_posts', array($this, 'doPostOrderBy'));
        add_action('pre_get_posts', array($this, 'doPostOrderDirection'));
        add_action('parse_query', array($this, 'handleQuery'));

        add_filter('option_posts_per_page', array($this, 'postsPerPage'), 1, 2);

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
     * List of allowed query strings
     *
     * @param array $vars
     * @return void
     */
    public function addQueryVars($vars)
    {
        if (!is_array($vars)) {
            $vars = [];
        }

        $vars[] = "orderby";
        $vars[] = "order";
        $vars[] = "from";
        $vars[] = "to";

        return $vars;
    }

    /**
     * Get filterable taxonomies
     * @return array Taxonomies
     * @todo: Refactor
     */
    public function getEnabledTaxonomies($postType = null, $group = true)
    {
        if (!$postType) {
            $postType = get_post_type();
        }

        $grouped    = array();
        $ungrouped  = array();
        $taxonomies = get_theme_mod('archive_' . sanitize_title($postType) . '_enabled_filters');

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
        if ($this->currentTaxonomy()) {
            $taxonomies = array_diff($taxonomies, (array)get_queried_object()->taxonomy);
        }

        foreach ($taxonomies as $key => $item) {
            if (!$tax = get_taxonomy($item)) {
                continue;
            }

            $terms = get_terms($item, array(
                'hide_empty' => false
            ));

            $placement = get_field(
                'archive_' . sanitize_title($postType) . '_filter_' . sanitize_title($item) . '_placement',
                'option'
            );

            if (is_null($placement)) {
                $placement = 'secondary';
            }

            $type = get_field(
                'archive_' . sanitize_title($postType) . '_filter_' . sanitize_title($item) . '_type',
                'option'
            );

            $grouped[$placement][$tax->name] = array(
                'label'        => $tax->label,
                'type'         => $type,
                'values'       => $terms,
                'hierarchical' => $tax->hierarchical
            );

            $ungrouped[$tax->name] = array(
                'label'        => $tax->label,
                'type'         => $type,
                'values'       => $terms,
                'hierarchical' => $tax->hierarchical
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

        if ((is_post_type_archive() || is_category() || is_date() || $this->currentTaxonomy() || is_tag()) && is_search()) {
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
     * @todo: Refactor
     */
    public function doPostTaxonomyFiltering($query)
    {
        // Do not execute this in admin view
        if (is_admin() || !(is_archive() || is_home() || is_category() || $this->currentTaxonomy() || is_tag()) || !$query->is_main_query()) {
            return $query;
        }

        $filterable = $this->getEnabledTaxonomies(
            $this->getCurrentPostType($query),
            false
        );

        if (empty($filterable) || !is_array($filterable)) {
            return $query;
        }
        $facetting = get_theme_mod('archive_' . $this->getCurrentPostType($query) . '_filter_type', false);
        if ($facetting == true) {
            $taxQuery = array('relation' => 'AND');
        } else {
            $taxQuery = array('relation' => 'OR');
        }
        foreach ($filterable as $key => $value) {
            if (!isset($_GET[$key]) || empty($_GET[$key]) || $_GET[$key] === '-1') {
                continue;
            }

            $taxQuery[] = array(
                'taxonomy' => $key,
                'field'    => 'slug',
                'terms'    => $this->getQueryString($key, false),
                'operator' => !empty($value['hierarchical']) ? 'IN' : 'AND'
            );
        }

        if ($this->currentTaxonomy() || is_category() || is_tag()) {
            $taxQuery = array(
                'relation' => 'AND',
                array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => get_queried_object()->taxonomy,
                        'field'    => 'slug',
                        'terms'    => (array) get_queried_object()->slug,
                        'operator' => 'IN'
                    )
                ),
                $taxQuery
            );
        }

        $query->set(
            'tax_query',
            apply_filters('Municipio/archive/tax_query', $taxQuery, $query)
        );
        return $query;
    }

    /**
     * Add where clause to post query based on active filters
     * @param  string $where Original where clause
     * @return string        Modified where clause
     */
    public function doPostDateFiltering($where)
    {
        if (is_admin()) {
            return $where;
        }

        global $wpdb;

        foreach (['from', 'to'] as $query) {
            if ($querys[$query] = $this->getQueryString($query)) {
                $querys[$query] = date(
                    'Y-m-d',
                    \strtotime($this->getQueryString($query))
                );
            } else {
                $querys[$query] = false;
            }
        }
        extract($querys);

        if ($from && $to) {
            $where .= " AND ($wpdb->posts.post_date >= '$from' AND $wpdb->posts.post_date <= '$to')";
        } elseif ($from && !$to) {
            $where .= " AND ($wpdb->posts.post_date >= '$from')";
        } elseif (!$from && $to) {
            $where .= " AND ($wpdb->posts.post_date <= '$to')";
        }

        return apply_filters('Municipio/archive/date_filter', $where, $from, $to);
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

    private function currentTaxonomy()
    {
        $queriedObject = get_queried_object();
        $isTaxArchive  = false;
        if (!empty($queriedObject->taxonomy) && isset($_SERVER['REQUEST_URI'])) {
            $pathParts   = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
            $trimmedPath = end($pathParts);
            if ($queriedObject->slug == $trimmedPath) {
                $isTaxArchive = $queriedObject->taxonomy;
            }
        }

        return $isTaxArchive;
    }

    /**
     * Retrieves the number of posts to display per page for the "post" post type.
     *
     * @param int $value The default number of posts to display per page.
     * @param string $name The name of the customizer value.
     *
     * @return int The number of posts to display per page for the "post" post type.
     */
    public function postsPerPage($value, $name)
    {
        if ($postsPerPage = get_theme_mod('archive_post_post_count', $value)) {
            return $postsPerPage;
        }
        return 10;
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
            if (is_array($_GET[$queryString])) {
                $sanitizedTerms = [];
                foreach ($_GET[$queryString] as $term) {
                    $sanitizedTerms[] = sanitize_text_field($term);
                }

                return $sanitizedTerms;
            }
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
        return !in_array($key, array('post_date', 'post_modified', 'post_title'));
    }

    /**
     * Determine if filter should be used here.
     *
     * @param WP_Query $query
     * @return boolean
     */
    private function shouldFilter($query)
    {
        if (is_admin() || !(is_archive() || is_home()) || !$query->is_main_query()) {
            return false;
        }
        return true;
    }
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
