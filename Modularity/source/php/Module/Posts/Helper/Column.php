<?php

namespace Modularity\Module\Posts\Helper;

/**
 * Class Column
 * @package Modularity\Module\Posts\Helper
 */
class Column
{
    /**
     * @param $postId
     * @param $tax
     * @return array|null
     */
    public static function getFirstColumnSize($columnSize)
    {
        switch ($columnSize) {
            case "o-grid-12@md":   //1-col
                return "o-grid-12@md";
            case "o-grid-6@md":    //2-col
                return "o-grid-12@md";
            case "o-grid-4@md":    //3-col
                return "o-grid-8@md";
            case "o-grid-3@md":    //4-col
                return "o-grid-6@md";
        }
    }
}
