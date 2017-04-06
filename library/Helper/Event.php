<?php

namespace Municipio\Helper;

class Event
{
    /**
     * Get single event date
     * @return array
     */
    public static function getSingleEventDate($post_id)
    {
        $date = null;
        $get_date = (! empty(get_query_var('date'))) ? get_query_var('date') : false;

        $occasions = self::getEventOccasions($post_id);
        if (count($occasions) == 1) {
            $date = self::formatShortDate($occasions[0]->start_date);
        } elseif ($get_date != false) {
            foreach ($occasions as $occasion) {
                $event_date = preg_replace('/\D/', '', $occasion->start_date);
                if ($get_date == $event_date) {
                    $date = self::formatShortDate($occasion->start_date);
                }
            }
        }

        return $date;
    }

    /**
     * Format short start date
     * @param  string $start_date occasion start date
     * @return array              date values
     */
    public static function formatShortDate($start_date)
    {
        $start = date('Y-m-d H:i:s', strtotime($start_date));
        $date = array(
                    'date'  => mysql2date('j', $start, true),
                    'month' => mysql2date('F', $start, true),
                    'time'  => mysql2date('H:i', $start, true),
                );

        return $date;
    }


    /**
     * Get event occasions
     * @param  int $post_id post id
     * @return array        object with occasions
     */
    public static function getEventOccasions($post_id)
    {
        global $wpdb;
        $db_table = $wpdb->prefix . "integrate_occasions";

        $query = "
        SELECT      *
        FROM        {$db_table}
        WHERE       {$db_table}.event_id = %d
        ";
        $query .= "ORDER BY {$db_table}.start_date ASC";

        $completeQuery = $wpdb->prepare($query, $post_id);
        $occasions = $wpdb->get_results($completeQuery);

        return $occasions;
    }

    /**
     * Format start and end date
     * @param  string $start_date occasion start date
     * @param  string $end_date   occasion end date
     * @return string             formatted date
     */
    public static function formatEventDate($start_date, $end_date)
    {
        $start = date('Y-m-d H:i:s', strtotime($start_date));
        $end = date('Y-m-d H:i:s', strtotime($end_date));
        $date = mysql2date('j F Y', $start, true) . ', ' . mysql2date('H:i', $start, true) . ' ' . __('to', 'municipio') . ' ' . mysql2date('j F Y', $end, true) . ', ' . mysql2date('H:i', $end, true);
        if (date('Y-m-d', strtotime($start)) == date('Y-m-d', strtotime($end))) {
            $date = mysql2date('j F Y', $start, true) . ', ' . mysql2date('H:i', $start, true) . ' - ' . mysql2date('H:i', $end, true);
        }

        return $date;
    }
}
