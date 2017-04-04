<?php

namespace Municipio\Controller;

class Archive extends \Municipio\Controller\BaseController
{
    private static $gridSize;

    private static $randomGridBase = array();
    private static $gridRow = array();
    private static $gridColumns = array();


    public function init()
    {
        $postType = get_post_type();
        if (is_author()) {
            $postType = 'author';
            $this->data['hasLeftSidebar'] = true;
        }

        $this->data['postType'] = $postType;
        $this->data['template'] = !empty(get_field('archive_' . sanitize_title($postType) . '_post_style', 'option')) ? get_field('archive_' . sanitize_title($postType) . '_post_style', 'option') : 'collapsed';
        $this->data['grid_size'] = !empty(get_field('archive_' . sanitize_title($postType) . '_grid_columns', 'option')) ? get_field('archive_' . sanitize_title($postType) . '_grid_columns', 'option') : 'grid-md-6';

        $this->data['grid_alter'] = get_field('archive_' . sanitize_title($postType) . '_grid_columns_alter', 'option') ? true : false;
        $this->data['gridSize'] = (int)str_replace('-', '', filter_var($this->data['grid_size'], FILTER_SANITIZE_NUMBER_INT));
        self::$gridSize = $this->data['gridSize'];

        if ($this->data['grid_alter']) {
            $this->gridAlterColumns();
        }
    }

    public function gridAlterColumns()
    {
        $gridRand = array();

        switch ($this->data['gridSize']) {
            case 12:
                $gridRand = array(
                    array(12)
                );
                break;

            case 6:
                $gridRand = array(
                    array(12),
                    array(6, 6),
                    array(6, 6)
                );
                break;

            case 4:
                $gridRand = array(
                    array(8, 4),
                    array(4, 4, 4),
                    array(4, 8)
                );
                break;

            case 3:
                $gridRand = array(
                    array(6, 3, 3),
                    array(3, 3, 3, 3),
                    array(3, 3, 6),
                    array(3, 3, 3, 3),
                    array(3, 6, 3)
                );
                break;

            default:
                $gridRand = array(
                    array(12)
                );
                break;
        }

        self::$randomGridBase = $gridRand;
    }

    public static function getColumnSize()
    {
        // Fallback if not set
        if (empty(self::$randomGridBase)) {
            return 'grid-md-' . self::$gridSize;
        }

        if (empty(self::$gridRow)) {
            self::$gridRow = self::$randomGridBase;
        }

        if (empty(self::$gridColumns)) {
            self::$gridColumns = self::$gridRow[0];
            array_shift(self::$gridRow);
        }

        $columnSize = 'grid-md-' . self::$gridColumns[0];
        array_shift(self::$gridColumns);

        return $columnSize;
    }

    public static function getColumnHeight()
    {
        switch (self::$gridSize) {
            case 3:
                return '280px';

            case 4:
                return '400px';

            case 6:
                return '500px';

            case 12:
                return '500px';

            default:
                return false;
        }

        return false;
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
