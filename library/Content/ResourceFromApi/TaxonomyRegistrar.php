<?php

namespace Municipio\Content\ResourceFromApi;

use WP_Taxonomy;

class TaxonomyRegistrar implements TypeRegistrarInterface
{
    public function register(string $name, array $arguments): bool
    {
        $objectType = $arguments['object_type'] ?? null;
        $preparedArguments = $this->prepareArguments($arguments);
        $registered = register_taxonomy($name, $objectType, $preparedArguments);
        
        return is_a($registered, WP_Taxonomy::class);
    }

    private function prepareArguments(array $arguments):array {
        $preparedArguments = $arguments;

        $preparedArguments['labels'] = [];
        $preparedArguments['labels']['name'] = $arguments['labels_name'];

        if(!empty($arguments['labels_singular_name'])) {
            $preparedArguments['labels']['singular_name'] = $arguments['labels_singular_name'];
        }

        return $preparedArguments;
    }
}
