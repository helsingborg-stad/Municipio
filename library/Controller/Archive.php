<?php

namespace Municipio\Controller;

/**
 * Class Archive
 *
 * @package Municipio\Controller
 */
class Archive extends \Municipio\Controller\BaseController
{
    private static $gridSize;

    private static $randomGridBase = array();
    private static $gridRow = array();
    private static $gridColumns = array();

    public function init()
    {
        parent::init();

        //Get current post type
        $postType = !empty($this->data['postType']) ? $this->data['postType'] : 'page';

        $this->data['displayArchiveLoop'] = true;

        //Get archive properties
        $this->data['archiveProps']             = $this->getArchiveProperties($postType, $this->data['customizer']);

        //Get template
        $template                               = $this->getTemplate($this->data['archiveProps']);
        $this->data['template']                 = $template;

        //The posts
        $this->data['posts']                    = $this->getPosts($template);
        $this->data['posts']                    = $this->getDate($this->data['posts'], $this->data['archiveProps']);
        $this->data['anyPostHasImage']          = $this->anyPostHasImage($this->data['posts']);

        //Set default values to query parameters
        $this->data['queryParameters']          = $this->setQueryParameters();

        //Filter options
        $this->data['taxonomyFilters']          = $this->getTaxonomyFilters($postType, $this->data['archiveProps']);

        $this->data['enableTextSearch']         = $this->enableTextSearch($this->data['archiveProps']);
        $this->data['enableDateFilter']         = $this->enableDateFilter($this->data['archiveProps']);
        $this->data['facettingType']            = $this->getFacettingType($this->data['archiveProps']);

        $this->data['displayFeaturedImage']     = $this->displayFeaturedImage($this->data['archiveProps']);
        $this->data['displayReadingTime']       = $this->displayReadingTime($this->data['archiveProps']);

        // Current term meta
        $this->data['currentTermColour']        = $this->getCurrentTermColour();
        $this->data['currentTermIcon']          = $this->getCurrentTermIcon();

        //Archive data
        $this->data['archiveTitle']             = $this->getArchiveTitle($this->data['archiveProps']);
        $this->data['archiveLead']              = $this->getArchiveLead($this->data['archiveProps']);
        $this->data['archiveBaseUrl']           = $this->getPostTypeArchiveLink($postType);
        $this->data['archiveResetUrl']          = $this->getPostTypeArchiveLink($postType);
        $this->data['gridColumnClass']          = $this->getGridClass($this->data['archiveProps']);

        //Pagination
        $this->data['currentPage']                     = $this->getCurrentPage();
        $this->data['paginationList']                  = $this->getPagination(
            $postType,
            $this->data['archiveBaseUrl'],
            $this->wpQuery
        );
        $this->data['paginationListPostsWithLocation'] = $this->getPagination(
            $postType,
            $this->data['archiveBaseUrl'],
            $this->wpQuery
        );

        $this->data['showPagination']                  = $this->showPagination($postType, $this->data['archiveBaseUrl'], $this->wpQuery);

        //Display functions
        $this->data['showFilterReset']          = $this->showFilterReset($this->data['queryParameters']);
        $this->data['showDatePickers']          = $this->showDatePickers($this->data['queryParameters']);

        //Facetting (menu)
        $this->data['hasQueryParameters']       = $this->hasQueryParameters(['paged' => true]);

        //Show filter?
        $this->data['showFilter']               = $this->showFilter($this->data['archiveProps']);

        //Archive menu
        $archiveMenu = new \Municipio\Helper\Navigation('archive-menu');
        $this->data['archiveMenuItems'] = $archiveMenu->getMenuItems(
            $postType . '-menu',
            false,
            false,
            true,
            true
        );

        //Language
        if (!isset($this->data['lang'])) {
            $this->data['lang'] = (object) [];
        }

        $this->data['lang']->noResult         = $this->data['postTypeDetails']->labels->not_found ?? __('No items found at this query.', 'municipio');
        $this->data['lang']->publish          = __('Published', 'municipio');
        $this->data['lang']->updated          = __('Updated', 'municipio');
        $this->data['lang']->readMore         = __('Read more', 'municipio');
        $this->data['lang']->searchFor        = ucfirst(strtolower($this->data['postTypeDetails']->labels->search_items));

        $this->data['lang']->fromDate         = __('Choose a from date', 'municipio');
        $this->data['lang']->toDate           = __('Choose a to date', 'municipio');
        $this->data['lang']->dateInvalid      = __('Select a valid date', 'municipio');

        $this->data['lang']->searchBtn        = __('Search', 'municipio');
        $this->data['lang']->filterBtn        = __('Filter', 'municipio');
        $this->data['lang']->resetSearchBtn   = __('Reset search', 'municipio');
        $this->data['lang']->resetFilterBtn   = __('Reset filter', 'municipio');
        $this->data['lang']->archiveNav       = __('Archive navigation', 'municipio');
        $this->data['lang']->resetFacetting   = __('Reset', 'municipio');
    }

    /**
     * Get the current therm colour
     */
    public function getCurrentTermColour()
    {
        if (!is_tax()) {
            return false;
        }
        $term = get_queried_object();
        return \Municipio\Helper\Term::getTermColour($term->term_id, $term->taxonomy);
    }

    /**
     * Get the current term icon
     */
    public function getCurrentTermIcon()
    {
        if (!is_tax()) {
            return false;
        }
        $term = get_queried_object();
        return \Municipio\Helper\Term::getTermIcon($term->term_id, $term->taxonomy);
    }

    /**
     * Get archive properties
     * @param  string $postType
     * @param  array $customizer
     * @return array|bool
     *
     * @deprecated since 3.0.0 In favour of \Municipio\Helper\Archive::getArchiveProperties()
     *
     */
    private function getArchiveProperties($postType, $customize)
    {
        return \Municipio\Helper\Archive::getArchiveProperties($postType, $customize);
    }

    /**
     * Camel case post type name
     *
     * @param string $postType
     * @return string
     *
     * @deprecated since 3.0.0 In favour of \Municipio\Helper\Archive::camelCasePostTypeName()
     *
     */
    private function camelCasePostTypeName($postType)
    {
        return \Municipio\Helper\Archive::camelCasePostTypeName($postType);
    }

    /**
     * Create a grid column size
     * @param  array $archiveProps
     * @return string
     */
    private function getGridClass($args): string
    {
        return \Municipio\Helper\Archive::getGridClass($args);
    }


    /**
     * Determines if view for filter should be rendered.
     * Check if any queryparameters is present
     * @param  array $exceptions Keys that shold be exceptions (do not take in account)
     * @return boolean
     */
    public function hasQueryParameters(array $exceptions = ['paged' => true])
    {
        return !empty(array_diff_key(
            (array) $_GET,
            (array) $exceptions
        ));
    }

    /**
     * Boolean function to determine if navigation should be shown
     *
     * @param string $postType
     * @return boolean
     */
    public function showFilter($args)
    {
        return \Municipio\Helper\Archive::showFilter($args);
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public function enableTextSearch($args)
    {
        return \Municipio\Helper\Archive::enableTextSearch($args);
    }

    /**
     * Boolean function to determine if date filter should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public function enableDateFilter($args)
    {
        return \Municipio\Helper\Archive::enableDateFilter($args);
    }

    /**
     * Get the current page
     *
     * @param   integer $default    Default page if not set
     * @return  integer             The current page
     */
    public function getCurrentPage(int $default = 1): int
    {
        return (get_query_var('paged')) ? get_query_var('paged') : $default;
    }

    /**
     * Get the template style for this archive
     *
     * @param string $postType  The post type to get the option from
     * @param string $default   The default value, if not found.
     *
     * @return string
     *
     * @deprecated since 3.0.0 In favour of \Municipio\Helper\Archive::getTemplate()
     *
     */
    public function getTemplate($args, string $default = 'cards'): string
    {
        return \Municipio\Helper\Archive::getTemplate($args, $default);
    }

    /**
     * Get the link to this page, without any query parameters
     *
     * @param   string  $postType   The current post type
     *
     * @return string
     */
    public function getPostTypeArchiveLink($postType)
    {
        $realPath       = (string) parse_url(home_url() . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $postTypePath   = (string) parse_url(get_post_type_archive_link($postType), PHP_URL_PATH);
        $mayBeTaxonomy  = (bool)   ($realPath != $postTypePath);

        if ($mayBeTaxonomy && is_a(get_queried_object(), 'WP_Term')) {
            return get_term_link(get_queried_object());
        }

        return get_post_type_archive_link($postType);
    }

    /**
     * Determines if the reset button should show or not.
     *
     * @return boolean
     */
    public function showFilterReset($queryParams): bool
    {
        return \Municipio\Helper\Archive::showFilterReset($queryParams);
    }

    /**
     * Determines if the date input toggle should default to show or not.
     *
     * @return boolean
     */
    public function showDatePickers($queryParams): bool
    {
        //From field
        if (isset($queryParams->from) && !empty($queryParams->from)) {
            return true;
        }

        //To field
        if (isset($queryParams->to) && !empty($queryParams->to)) {
            return true;
        }

        return false;
    }

    /**
     * Get the archive title
     *
     * @return string
     */
    protected function getArchiveTitle($args)
    {
        return (string) \apply_filters(
            'Municipio/Controller/Archive/getArchiveTitle',
            $args->heading ?? ''
        );
    }

    /**
     * Get the archive lead
     *
     * @return string
     */
    protected function getArchiveLead($args)
    {
        return (string) \apply_filters(
            'Municipio/Controller/Archive/getArchiveLead',
            $args->body ?? ''
        );
    }

    /**
     * Get pagination
     *
     * @return array    Pagination array with label and link
     */
    protected function getPagination($postType, $archiveBaseUrl, $wpQuery)
    {
        return \Municipio\Helper\Archive::getPagination($archiveBaseUrl, $wpQuery);
    }

    /**
     * If the pagination should show or no
     *
     * @return bool
     */
    protected function showPagination($postType, $archiveBaseUrl, $wpQuery)
    {
        return \Municipio\Helper\Archive::showPagination($archiveBaseUrl, $wpQuery);
    }

    /**
     * Build a query string with page numer
     *
     * @param integer $number
     * @return void
     */
    protected function setQueryString($number)
    {
        return \Municipio\Helper\Archive::setQueryString($number);
    }

     /**
     * Set default values for query parameters
     *
     * @return void
     */
    public function setQueryParameters(array $data = [])
    {
        $queryParameters = [
        'search' =>  isset($_GET['s']) ? $_GET['s'] : '',
        'from' =>  isset($_GET['from']) ? $_GET['from'] : '',
        'to' =>  isset($_GET['to']) ? $_GET['to'] : ''
        ];

        if (!empty($data) && !empty($data['postType'])) {
            //Include taxonomies (dynamic)
            $taxonomies = get_object_taxonomies($data['postType']);

            if (is_array($taxonomies) && !empty($taxonomies)) {
                foreach ($taxonomies as $taxonomy) {
                    $queryParameters[$taxonomy] = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
                }
            }
        }

        return \apply_filters(
            'Municipio/Controller/Archive/setQueryParameters',
            (object) $queryParameters
        );
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    public function getFacettingType($args)
    {
        return \Municipio\Helper\Archive::getFacettingType($args);
    }

    public function displayReadingTime($args)
    {
        return \Municipio\Helper\Archive::displayReadingTime($args);
    }
    public function displayFeaturedImage($args)
    {
        return \Municipio\Helper\Archive::displayFeaturedImage($args);
    }

    /**
     * Get taxonomy filters to show
     *
     * @param   string  $postType           The current post type
     * @return  array   $taxonomyObjects    Array containing selects with options
     */
    protected function getTaxonomyFilters($postType, $args)
    {
        if (!isset($args->enabledFilters) || empty($args->enabledFilters)) {
            return [];
        }

        //Define storage point
        $taxonomyObjects = [];

        //Get active taxonomy filters
        $taxonomies = apply_filters('Municipio/Archive/getTaxonomyFilters/taxonomies', array_diff(
            $args->enabledFilters,
            [$this->currentTaxonomy()]
        ), $this->currentTaxonomy());

        if (is_array($taxonomies) && !empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                //Fetch full object
                $taxonomy = get_taxonomy($taxonomy);

                //Bail if not found
                if ($taxonomy === false) {
                    continue;
                }

                //Get terms
                $terms = get_terms(
                    array(
                    'taxonomy' => $taxonomy->name,
                    'hide_empty' => true
                    )
                );

                //Bail early if there isen't any options
                if (empty($terms)) {
                    continue;
                }

                //Reset options
                $options = [];

                //Fill options
                if (is_array($terms) && !empty($terms)) {
                    foreach ($terms as $option) {
                        if (!empty($option->name)) {
                            $options[$option->slug] = ucfirst($option->name) . " (" . $option->count . ")";
                        }
                    }
                }

                $tax = \Municipio\Helper\FormatObject::camelCase($taxonomy->name);

                //Data
                $taxonomyObject = [
                    'label' => (__("Select", 'municipio') . " " . strtolower($taxonomy->labels->singular_name)),
                    'required' => false,
                    'attributeList' => [
                        'type' => 'text',
                        'name' => $taxonomy->name
                    ],
                    'fieldType' => $args->{$tax . "FilterFieldType"} ?? 'single',
                    'options' => $options
                ];

                if (isset($_GET[$taxonomy->name])) {
                    $taxonomyObject['preselected'] = $this->preselectAllTaxonomiesInUrl($taxonomy->name);
                }

                $taxonomyObjects[] = $taxonomyObject;
            }
        }

        return \apply_filters('Municipio/Controller/Archive/getTaxonomies', $taxonomyObjects);
    }

    private function preselectAllTaxonomiesInUrl($taxonomyName) {
        $preselected = $_GET[$taxonomyName];
        return !empty($preselected) ? $preselected : [];
    }

    /**
     * Get the current taxonomy page
     */
    private function currentTaxonomy()
    {
        $queriedObject = get_queried_object();

        if (!empty($queriedObject->taxonomy)) {
            return $queriedObject->taxonomy;
        }
        return false;
    }

    /**
     * Get posts in expected format for each component.
     *
     * @param   string  $template  The template identifier
     *
     * @return  array   $items     Array of posts
     */
    public function getPosts($template): array
    {
        $items = null;
        if (is_array($this->posts) && !empty($this->posts)) {
            if ($template == 'list') {
                $items = $this->getListItems($this->posts);
            } else {
                $items = $this->getArchiveItems($this->posts);
            }

            return \apply_filters('Municipio/Controller/Archive/getArchivePosts', $items);
        }

        return [];
    }

    /**
     * Prepare posts for general output
     *
     * @param   array $posts    The posts
     * @return  array           The posts - formatted
     */
    protected function getArchiveItems(array $posts): array
    {
        $preparedPosts = [];

        if (is_array($posts) && !empty($posts)) {
            foreach ($posts as $post) {
                $post                   = \Municipio\Helper\Post::preparePostObject($post);
                $post->href             = $post->permalink;
                $post->excerpt          = $post->postExcerpt;
                $preparedPosts[] = $post;
            }
        }

        return $preparedPosts;
    }

    /**
     * Prepare a date to show in view
     *
     * @param   array $posts    The posts
     * @return  array           The posts - with archive date
     */
    public function getDate($posts, $archiveProps)
    {
        $preparedPosts = [];

        //Set defaults
        if (is_array($posts) && !empty($posts)) {
            foreach ($posts as $post) {
                if (!is_object($post)) {
                    continue;
                }
                $post->archiveDateFormat = $archiveProps->dateFormat ?? 'default';
                $post->archiveDate = false;
            }
        }

        if (!isset($archiveProps->dateField) || is_null($archiveProps->dateField) || $archiveProps->dateField === 'none') {
            return $posts;
        }

        $isMetaKey = in_array($archiveProps->dateField, ['post_date', 'post_modified']) ? false : true;

        if ($isMetaKey == true) {
            $targetFieldName = $archiveProps->dateField;
        } else {
            $targetFieldName = \Municipio\Helper\FormatObject::camelCase($archiveProps->dateField) . 'Gmt';
        }

        if (is_array($posts) && !empty($posts)) {
            foreach ($posts as $post) {
                if (!is_object($post)) {
                    continue;
                }

                //Defaults 
                $post->archiveDateFormat = $archiveProps->dateFormat ?? 'default';
                $post->archiveDate = false;

                if (!is_null($archiveProps->dateField)) {
                    if ($isMetaKey === true) {
                        $post->archiveDate = get_post_meta($post->id, $post, true);
                    } elseif (isset($post->{$targetFieldName})) {
                        $post->archiveDate = $post->{$targetFieldName};
                    }
                }

                $post->archiveDate = apply_filters('Municipio/Controller/Archive/getDate', $post->archiveDate, $post);
                if (!isset($post->archiveDate)) {
                    $post->archiveDate = false;
                } else {
                    $post->archiveDate = wp_date(
                        $this->getDateFormatString($archiveProps->dateFormat),
                        strtotime($post->archiveDate),
                    );
                }

                $post->archiveDateFormat = $archiveProps->dateFormat ?? 'default';

                $preparedPosts[] = $post;
            }
        }
        return $preparedPosts;
    }

    /**
     * Switch between different date formats
     *
     * @param string $key
     * @return string $dateFormat
     */
    private function getDateFormatString(string $key): string
    {
        switch ($key) {
            case 'date':
                return get_option('date_format');
            case 'date-time':
                return get_option('date_format') . " " . get_option('time_format');
            case 'date-badge':
                return "Y-m-d";
            default:
                return get_option('date_format') . " " . get_option('time_format');
        }
    }

    /**
     * Prepare posts for list output
     *
     * @param   array $posts    The posts
     * @return  array           The posts - formatted
     */
    protected function getListItems(array $posts): array
    {
        $dateFormat = \Municipio\Helper\DateFormat::getDateFormat('date');
        $preparedPosts = [
        'items' => [],
        'headings' => ['Title', 'Published', 'Updated']
        ];

        if (is_array($posts) && !empty($posts)) {
            foreach ($posts as $post) {
                $post           = \Municipio\Helper\Post::preparePostObject($post);
                $postDate       = \date($dateFormat, strtotime($post->postDate));
                $postModified   = \date($dateFormat, strtotime($post->postModified));

                $preparedPosts['items'][] =
                [
                    'id' => $post->id,
                    'href' => get_permalink($post->id),
                    'columns' => [
                        $post->postTitle,
                        $post->post_date = $postDate,
                        $post->post_modified = $postModified
                    ]
                ];
            }
        }

        return $preparedPosts;
    }
}
