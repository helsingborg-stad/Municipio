<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost\Types;

use Municipio\ExternalContent\SchemaObjectToWpPost\ISchemaObjectToWpPost;
use Spatie\SchemaOrg\Thing as ThingSchema;
use WP_Post;

class Thing implements ISchemaObjectToWpPost
{
    private WP_Post $post;

    public function __construct(private ThingSchema $schemaObject)
    {
        $this->post = new WP_Post((object)[]);
    }

    public function toWpPost(): WP_Post
    {
        $this->post->ID           = $this->schemaObject['identifier'];
        $this->post->post_title   = $this->schemaObject['name'];
        $this->post->post_content = $this->schemaObject['description'];
        $this->post->post_type    = 'thing';

        return $this->post;
    }
}
