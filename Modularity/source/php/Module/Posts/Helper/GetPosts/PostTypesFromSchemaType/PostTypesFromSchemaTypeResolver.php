<?php

namespace Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType;

class PostTypesFromSchemaTypeResolver implements PostTypesFromSchemaTypeResolverInterface
{
    public function __construct(private PostTypesFromSchemaTypeResolverInterface $nextResolver = new NullPostTypesFromSchemaTypeResolver())
    {
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $schemaType): array
    {
        $class = '\Municipio\SchemaData\Helper\GetSchemaType';
        $method = 'getPostTypesFromSchemaType';
        
        if(class_exists($class) && method_exists($class, $method)) {
            $postTypes = call_user_func([new $class(), $method], $schemaType);
            
            if( is_array($postTypes) ) {
                return $postTypes;
            }
        }

        return $this->nextResolver->resolve($schemaType);
    }
}