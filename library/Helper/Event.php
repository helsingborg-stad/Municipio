<?php

namespace Municipio\Helper;

class Event
{
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
