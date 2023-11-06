<?php

namespace Municipio\Content\ResourceFromApi\PostType;

use Municipio\Content\ResourceFromApi\TypeRegistrarInterface;
use WP_Post_Type;

class PostTypeRegistrar implements TypeRegistrarInterface
{
    public function register(string $name, array $arguments): bool
    {
        $preparedArguments = $this->prepareArguments($arguments);
        $registered = register_post_type($name, $preparedArguments);
        return is_a($registered, WP_Post_Type::class);
    }

    private function prepareArguments(array $arguments):array {
        $preparedArguments = $arguments;

        if( isset($arguments['labels_singular_name']) && !empty($arguments['labels_singular_name']) ) {
            $preparedArguments['labels'] = ['singular_name' => $arguments['labels_singular_name']];
        }

        if( empty($arguments['query_var']) ) {
            unset($preparedArguments['query_var']);
        }

        if( $arguments['rewrite'] === true ) {
            $preparedArguments['rewrite'] = $arguments['rewrite_options'];

            if( empty($arguments['rewrite_options']['slug']) ) {
                unset($preparedArguments['rewrite']['slug']);
            }
        }

        if( !$arguments['hierarchical'] && !empty($arguments['parent_post_types']) ) {
            
            $parentSlug = '/%parentPost%';
            $slug = '';
            
            if( isset($preparedArguments['rewrite']) && isset($preparedArguments['rewrite']['slug']) ) {
                $slug = $preparedArguments['rewrite']['slug'];
                $slug = ltrim($slug, '/');
            } 

            $preparedArguments['rewrite']['slug'] = "{$parentSlug}{$slug}";
        }

        return $preparedArguments;
    }
}
