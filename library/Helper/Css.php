<?php

namespace Municipio\Helper;

class Css
{
    public static function breakpoints($returnKeys = false)
    {
        $breakpoints = apply_filters('Municipio/Helper/Css/Breakpoints',
            array(
                'xs' => 'xs',
                'sm' => 'sm',
                'md' => 'md',
                'lg' => 'lg'
            )
        );

        $breakpoints = ($returnKeys == true) ? array_keys($breakpoints) : $breakpoints;

        return $breakpoints;
    }

    /**
     * Generates grid classes
     * @param  mixed (int/string/array) $columnSize Column size (defaults to 'all')
     * @param  mixed (string/array) $screen Breakpoints (defaults to 'all')
     * @return mixed (string/array) CSS grid classes
     */
    public static function grid($columnSize = 'all', $screen = 'all')
    {
        if (!is_array(self::breakpoints()) || empty(self::breakpoints())) {
            return;
        }

        $breakpoints = self::breakpoints();
        $columns = apply_filters('Municipio/Helper/Css/Grid/Columns', 12, $columnSize, $screen);
        $pattern = apply_filters('Municipio/Helper/Css/Grid/Pattern', 'grid-%s-%d', $columnSize, $screen);

        //Convert $screen to array
        $screen = (is_string($screen) && $screen != 'all') ? array($screen) : $screen;

        //Set breakpoints
        if ($screen != 'all') {
            foreach ($breakpoints as $i => $breakpoint) {
                if (!in_array($breakpoint, $screen)) {
                    unset($breakpoints[$i]);
                }
            }
        }

        //Convert $columnSize to array
        $columnSize = (!is_array($columnSize) && $columnSize != 'all') ? array($columnSize) : $columnSize;

        //Create grid classes
        $grid = array();

        foreach ($breakpoints as $breakpoint) {
            if ($columnSize == 'all') {
                for ($i = 1; $i <= $columns; $i++) {
                    $grid[] = sprintf($pattern, $breakpoint, $i);
                }
            } else {
                foreach ($columnSize as $column) {
                    if ($column <= $columns) {
                        $grid[] = sprintf($pattern, $breakpoint, $column);
                    }
                }
            }
        }

        //Convert to string if grid only has 1 item
        $grid = (count($grid) == 1) ? $grid[0] : $grid;

        if (!empty($grid) && is_array($grid) || !empty($grid) && is_string($grid)) {
            return $grid;
        }

        return false;
    }

    public static function hidden()
    {
        if (!is_array(self::breakpoints(true)) || empty(self::breakpoints(true))) {
            return;
        }

        $pattern = apply_filters('Municipio/Helper/Css/Breakpoints', 'hidden-%s');
        $hidden = array();

        foreach (self::breakpoints(true) as $breakpoint) {
            $hidden[$breakpoint] = sprintf($pattern, $breakpoint);
        }

        if (is_array($hidden) && !empty($hidden)) {
            return $hidden;
        }
    }

    public static function container()
    {
        $containers = apply_filters('Municipio/Helper/Css/Container', array(
            'content' => 'container',
            'full width' => 'container-fullwidth'
        ));

        return $containers;
    }
}


