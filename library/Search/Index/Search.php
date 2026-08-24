<?php

namespace Municipio\Search\Index;

use \Municipio\Search\Index\Helper\Index as Instance;

class Search
{
    public function __construct()
    {
        add_action('pre_get_posts', array($this, 'doAlgoliaQuery'));
    }

    /**
     * Do algolia query
     *
     * @param $query
     * @return void
     */
    public function doAlgoliaQuery($query)
    {
        if (!is_admin() && $query->is_main_query() && $query->is_search && self::isSearchPage()) {
            //Check if backend search should run or not
            if (self::backendSearchActive()) {
                $query->query_vars['post__in'] = self::getPostIdArray(
                    Instance::getIndex()->search($query->query['s'])['hits'],
                );

                //Disable local search
                $query->query_vars['s'] = false;

                //Order by respomse order algolia
                $query->set('orderby', 'post__in');
            }

            //Query (locally) for a post that dosen't exist, if empty response from algolia
            if (!self::backendSearchActive()) {
                $query->query_vars['post__in'] = [PHP_INT_MAX]; //Fake post id
                $query->set('posts_per_page', 1); //Limit to 1 result
            }
        }
    }

    /**
     * Get id's if result array
     *
     * @param   array $response   The full response array
     * @return  array             Array containing results
     */
    private static function getPostIdArray($response)
    {
        $result = array();
        foreach ($response as $item) {
            $result[] = $item['ID'];
        }

        return $result;
    }

    /**
     * Check if search page is active page
     *
     * @return boolean
     */
    private static function isSearchPage()
    {
        if (is_multisite() && (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL === false)) {
            if (trim(strtok($_SERVER['REQUEST_URI'], '?'), '/') == trim(get_blog_details()->path, '/') && is_search()) {
                return true;
            }
        }

        if (trim(strtok($_SERVER['REQUEST_URI'], '?'), '/') == '' && is_search()) {
            return true;
        }

        return false;
    }

    /**
     * Check if backend search should run
     *
     * @return boolean
     */
    private static function backendSearchActive()
    {
        //Backend search active
        $backendSearchActive = apply_Filters('AlgoliaIndex/BackendSearchActive', true);

        //Query algolia for search result
        if ($backendSearchActive || is_post_type_archive()) {
            return true;
        }

        return false;
    }
}
