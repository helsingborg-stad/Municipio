<?php

namespace Municipio\ExternalContent\WpTermFactory;

use Municipio\Schema\BaseType;
use WP_Term;

/**
 * Class WpTermFactory
 *
 * @package Municipio\ExternalContent\WpTermFactory
 */
class WpTermFactory implements WpTermFactoryInterface
{
    /**
     * WpTermFactory constructor.
     */
    public function create(BaseType|string $schemaObject, string $taxonomy): WP_Term
    {
        return new WP_Term((object)[
            'taxonomy' => $taxonomy,
            'name'     => is_string($schemaObject) ? $schemaObject : ''
        ]);
    }
}
