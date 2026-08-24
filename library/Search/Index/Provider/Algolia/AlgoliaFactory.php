<?php

namespace Municipio\Search\Index\Provider\Algolia;

use Municipio\Search\Index\Provider\AbstractProvider;

class AlgoliaFactory
{
    public static function createFromEnv(): AbstractProvider
    {
        return new AlgoliaProvider(
            \Municipio\Search\Index\Helper\Options::applicationId(),
            \Municipio\Search\Index\Helper\Options::apiKey(),
            \Municipio\Search\Index\Helper\Options::indexName(),
        );
    }
}
