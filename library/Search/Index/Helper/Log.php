<?php

declare(strict_types=1);


namespace Municipio\Search\Index\Helper;

class Log
{
    private static $heading = 'Algolia Index: ';

    /**
     * Write error
     *
     * @return void
     */
    public static function error($message)
    {
        error_log(self::$heading . $message);
    }
}
