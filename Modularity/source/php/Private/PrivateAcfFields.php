<?php

declare(strict_types=1);

namespace Modularity\Private;

class PrivateAcfFields
{
    public function __construct()
    {
        add_filter('Municipio/Private/PrivateAcfFields/fields', static function ($fields) {
            $fields[] = 'field_67126c170c176';
            $fields[] = 'field_67813612eb109';

            return $fields;
        });
    }
}
