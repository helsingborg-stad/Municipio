<?php

namespace Municipio\Content\ResourceFromApi;

use WP_Taxonomy;

class PostTypeRegistrar implements TypeRegistrarInterface
{
    public function register(string $name, array $arguments): bool
    {
        $registered = register_taxonomy($name, $arguments);
        
        return is_a($registered, WP_Taxonomy::class);
    }
}
