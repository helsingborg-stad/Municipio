<?php

namespace Municipio\Theme;

class EventArchive extends Archive
{

    private $eventPostType = "event";

    public function __construct()
    {
        add_action('pre_get_posts', array($this, 'filterEvents'), 100);
    }

    /**
     * Filter events
     * @param  object $query object WP Query
     */
    public function filterEvents($query)
    {
        if (is_admin() || ! is_post_type_archive($this->eventPostType)) {
            return $query;
        }

        $query->set('posts_per_page', 50);

        add_filter('posts_fields', array($this, 'eventFilterSelect'));
        add_filter('posts_join', array($this, 'eventFilterJoin'));
        add_filter('posts_where', array($this, 'eventFilterWhere'), 10, 2);
        add_filter('posts_groupby', array($this, 'eventFilterGroupBy'));
        add_filter('posts_orderby', array($this, 'eventFilterOrderBy'));

        return $query;
    }

    /**
     * Select tables
     * @param  string $select Original query
     * @return string         Modified query
     */
    public function eventFilterSelect($select)
    {
        global $wpdb;
        $db_table = $wpdb->prefix . "integrate_occasions";

        $select .= ",{$db_table}.start_date,{$db_table}.end_date,{$db_table}.door_time,{$db_table}.status,{$db_table}.exception_information,{$db_table}.content_mode,{$db_table}.content ";

        return $select;
    }

    /**
     * Join taxonomies and postmeta to sql statement
     * @param  string $join current join sql statement
     * @return string       updated statement
     */
    public function eventFilterJoin($join)
    {
        global $wp_query, $wpdb;
        $db_table = $wpdb->prefix . "integrate_occasions";
        $join .= "LEFT JOIN {$db_table} ON ({$wpdb->posts}.ID = {$db_table}.event_id) ";

        return $join;
    }

    /**
     * Add where statements
     * @param  string $where current where statement
     * @return string        updated statement
     */
    public function eventFilterWhere($where)
    {
        global $wpdb;
        $db_table = $wpdb->prefix . "integrate_occasions";

        $from = null;
        $to = null;

        if (isset($_GET['from']) && !empty($_GET['from'])) {
            $from = sanitize_text_field($_GET['from']);
        }

        if (isset($_GET['to']) && !empty($_GET['to'])) {
            $to = sanitize_text_field($_GET['to']);
        }

        if (!is_null($from) && !is_null($to)) {
            // USE BETWEEN ON START DATE
            $where = str_replace(
                "{$wpdb->posts}.post_date >= '{$from}'",
                "{$db_table}.start_date BETWEEN '{$from}' AND '{$to}'",
                $where
            );
            $where = str_replace(
                "AND {$wpdb->posts}.post_date <= '{$to}'",
                "",
                $where
            );
        } elseif (!is_null($from) || !is_null($to)) {
            // USE FROM OR TO
            $where = str_replace("{$wpdb->posts}.post_date >=", "{$db_table}.start_date >=", $where);
            $where = str_replace("{$wpdb->posts}.post_date <=", "{$db_table}.end_date <=", $where);
        }

        return $where;
    }

    /**
     * Add group by statement
     * @param  string $groupby current group by statement
     * @return string          updated statement
     */
    public function eventFilterGroupBy($groupby)
    {
        global $wpdb;
        $db_table = $wpdb->prefix . "integrate_occasions";
        $groupby = "{$db_table}.start_date, {$db_table}.end_date";

        return $groupby;
    }

    /**
     * Add group by statement
     * @param  string $groupby current group by statement
     * @return string          updated statement
     */
    public function eventFilterOrderBy($orderby)
    {
        global $wpdb;
        $db_table = $wpdb->prefix . "integrate_occasions";
        $orderby = "{$db_table}.start_date ASC";

        return $orderby;
    }

}
