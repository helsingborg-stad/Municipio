<?php

namespace Intranet\Search;

class Elasticsearch
{
    public static $level = 'all';

    public function __construct()
    {
        if (isset($_GET['level']) && !empty($_GET['level'])) {
            self::$level = sanitize_text_field($_GET['level']);
        }

        add_action('pre_get_posts', array($this, 'setSites'), 1000);
        add_action('pre_get_posts', array($this, 'setTypes'), 1000);
        add_action('pre_get_posts', array($this, 'setOrderby'), 1000);
        add_action('pre_get_posts', array($this, 'removeMime'), 1000);

        add_filter('ep_indexable_post_status', array($this, 'indexablePostStatuses'));
        add_filter('ep_indexable_post_types', array($this, 'indexablePostTypes'));
        add_filter('ep_search_args', array($this, 'searchArgs'), 10, 3);
    }

    /**
     * Remove specified mime types
     *  @param WP_Query $query
     */
     */
    public function removeMime($query)
    {

        //TODO: FIX!
        // If not search or main query, return the default query
        if (!is_search() || !$query->is_main_query()) {
            return;
        }

        // Set orderby
        $query->set('orderby', 'relevance');
    }

    /**
     * Indexable post statuses
     * @param  array $statuses Default post statuses
     * @return array           Updated post statuses
     */
    public function indexablePostStatuses($statuses)
    {
        $statuses[] = 'private';
        $statuses[] = 'inherit';

        return array_unique($statuses);
    }

    /**
     * Indexable post types
     * @param  array $types Default post types
     * @return array Updated post types
     */
    public function indexablePostTypes($types)
    {
        return array_unique(array_merge($types, \Intranet\Helper\PostType::getPublic()));
    }

    public function searchArgs($args, $scope, $query_args)
    {
        $q = $query_args['s'];

        $query = array(
            'should' => array(
                array(
                    'multi_match' => array(
                        'query' => $q,
                        'fields' => array(
                            'post_title^7',
                            'post_content^3',
                        ),
                        'fuzziness' => 'auto'
                    )
                )
            )
        );

        $args['query']['function_score']['query']['bool'] = $query;

        /*
        $args['query'] = array(
            'simple_query_string' => array(
                'fields' => array('post_title^7', 'post_content^3'),
                'query' => $q
            )
        );
        */


        return $args;
    }

    /**
     * Set which sites to search in
     * @param WP_Query $query
     */
    public function setSites($query)
    {
        // If not search or main query, return the default query
        if (!is_search() || !$query->is_main_query()) {
            return;
        }

        // If level is users abort the query
        if (self::$level === 'users') {
            $query = false;
            return;
        }

        // Set sites if non of above
        $query->set('sites', self::getSitesFromLevel(self::$level));
    }

    /**
     * Set which post types and statuses to search for
     * @param WP_Query $query
     */
    public function setTypes($query)
    {
        // If not search or main query, return the default query
        if (!is_search() || !$query->is_main_query()) {
            return;
        }

        // If level is users abort the query
        if (self::$level === 'users') {
            $query = false;
            return;
        }

        $query->set('cache_results', false);
        $query->set('post_type', \Intranet\Helper\PostType::getPublic());

        $postStatuses  = array('publish', 'inherit');

        if (is_user_logged_in()) {
            $postStatuses[] = 'private';
        }

        $query->set('post_status', $postStatuses);
    }

    /**
     * Set orderby to relevance
     * @param WP_Query $query
     */
    public function setOrderby($query)
    {
        // If not search or main query, return the default query
        if (!is_search() || !$query->is_main_query()) {
            return;
        }

        // If level is users abort the query
        if (self::$level === 'users') {
            $query = false;
            return;
        }

        // Set orderby
        $query->set('orderby', 'relevance');
    }

    /**
     * Get sites to search from a specific level string
     * @param  string $level Level
     * @return string|array  Sites to search
     */
    public static function getSitesFromLevel($level)
    {
        switch ($level) {
            case 'subscriptions':
                $sites = array_merge(
                    \Intranet\User\Subscription::getSubscriptions(get_current_user_id(), true),
                    \Intranet\User\Subscription::getForcedSubscriptions(true)
                );
                return $sites;
                break;

            case 'current':
                return 'current';
                break;

            default:
                return 'all';
                break;
        }
    }
}
