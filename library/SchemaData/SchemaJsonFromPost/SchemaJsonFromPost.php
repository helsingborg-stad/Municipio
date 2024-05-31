<?php

namespace Municipio\SchemaData\SchemaJsonFromPost;

use Municipio\SchemaData\Utils\GetSchemaTypeFromPostType;
use WP_Post;

class SchemaJsonFromPost implements SchemaJsonFromPostInterface {
    
    public function __construct(private GetSchemaTypeFromPostType $util)
    {
        
    }

    public function create(WP_Post $postId): string
    {
        $schemaType = $this->util->getSchemaTypeFromPostType($postId->post_type);

        if( empty($schemaType) ) {
            return '';
        }

        $schemaData = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
        ];

        return json_encode($schemaData);
    }
}