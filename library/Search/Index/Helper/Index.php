<?php

namespace Municipio\Search\Index\Helper;

use \Municipio\Search\Index\Helper\Options as Options;
use \Municipio\Search\Index\Provider\AbstractProvider;
use Municipio\Search\Index\Provider\ProviderFactory;

class Index
{
    private static $_index = null;

    /**
     * Get the index
     *
     * @return AbstractProvider
     */
    public static function getIndex(): AbstractProvider
    {
        if (!is_null(self::$_index)) {
            return self::$_index;
        }

        return self::$_index = ProviderFactory::createFromEnv();
    }
}
