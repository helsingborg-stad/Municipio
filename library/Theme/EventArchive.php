<?php

namespace Municipio\Theme;

class EventArchive extends Archive
{

    private $eventPostType = "event";
    private $db_table;

    public function __construct()
    {
        //Setup local wpdb instance
        global $wpdb;
        $this->db = $wpdb;
        $this->db_table = $wpdb->prefix . "integrate_occasions";

        //Run functions if table exists
        if ($this->db->get_var("SHOW TABLES LIKE '" . $this->db_table . "'") !== null) {
            add_action('pre_get_posts', array($this, 'filterEvents'), 100);
        }

        add_filter('post_type_link', array($this, 'addEventDateQueryArgToPermalinks'), 10, 3);
    }

    /**
     * Add date params to permalinks
     */
    public function addEventDateQueryArgToPermalinks($permalink, $post, $leavename)
    {
        if (!isset($post->start_date) || $post->post_type != 'event') {
            return $permalink;
        }

        return esc_url(add_query_arg('date', preg_replace('/\D/', '', $post->start_date), $permalink));
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
        return $select . ",{$this->db_table}.start_date,{$this->db_table}.end_date,{$this->db_table}.door_time,{$this->db_table}.status,{$this->db_table}.exception_information,{$this->db_table}.content_mode,{$this->db_table}.content ";
    }

    /**
     * Join taxonomies and postmeta to sql statement
     * @param  string $join current join sql statement
     * @return string       updated statement
     */
    public function eventFilterJoin($join)
    {
        return $join . "LEFT JOIN {$this->db_table} ON ({$this->db->posts}.ID = {$this->db_table}.event_id) ";
    }

    /**
     * Add where statements
     * @param  string $where current where statement
     * @return string        updated statement
     */
    public function eventFilterWhere($where)
    {
        $from = null;
        $to = null;

        if (isset($_GET['from']) && !empty($_GET['from'])) {
            $from = sanitize_text_field($_GET['from']);
        }

        if (isset($_GET['to']) && !empty($_GET['to'])) {
            $to = date('Y-m-d', strtotime("+1 day", strtotime(sanitize_text_field($_GET['to']))));
        }

        if (!is_null($from) && !is_null($to)) {
            // USE BETWEEN ON START DATE
            $where = str_replace(
                "{$this->db->posts}.post_date >= '{$from}'",
                "{$this->db_table}.start_date BETWEEN CAST('{$from}' AS DATE) AND CAST('{$to}' AS DATE)",
                $where
            );
            $where = str_replace(
                "AND {$this->db->posts}.post_date <= '{$to}'",
                "",
                $where
            );
        } elseif (!is_null($from) || !is_null($to)) {
            // USE FROM OR TO
            $where = str_replace("{$this->db->posts}.post_date >=", "{$this->db_table}.start_date >=", $where);
            $where = str_replace("{$this->db->posts}.post_date <=", "{$this->db_table}.end_date <=", $where);
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
        $groupby = "{$wpdb->posts}.ID ,{$this->db_table}.start_date, {$this->db_table}.end_date";

        return $groupby;
    }

    /**
     * Add group by statement
     * @param  string $groupby current group by statement
     * @return string          updated statement
     */
    public function eventFilterOrderBy($orderby)
    {
        return "{$this->db_table}.start_date ASC";
    }

}
