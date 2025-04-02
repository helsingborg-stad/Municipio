<?php

namespace Municipio\ExternalContent\WpTermFactory;

use Municipio\Schema\BaseType;
use WP_Term;

interface WpTermFactoryInterface
{
    /**
     * Create a WP_Term object.
     *
     * @param BaseType|string $schemaObject The schema object or a string to use as term name/title.
     * @param string $taxonomy The taxonomy name.
     * @return WP_Term The created WP_Term object.
     */
    public function create(BaseType|string $schemaObject, string $taxonomy): WP_Term;
}
