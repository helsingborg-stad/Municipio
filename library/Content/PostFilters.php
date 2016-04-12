<?php

namespace Municipio\Content;

class PostFilters
{
    public function __construct()
    {
        add_filter('posts_where', array($this, 'doPostFiltering'));
    }

    /**
     * Add where clause to post query based on active filters
     * @param  string $where Original where clause
     * @return string        Modified where clause
     */
    public function doPostFiltering($where)
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

        return $where;
    }
}
