<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost\Types;

use Municipio\ExternalContent\SchemaObjectToWpPost\ISchemaObjectToWpPost;
use Spatie\SchemaOrg\Event as EventSchema;
use WP_Post;

class Event implements ISchemaObjectToWpPost
{
    private WP_Post $post;

    public function __construct(private EventSchema $schemaObject)
    {
        $this->post = new WP_Post((object)[]);
    }

    public function toWpPost(): WP_Post
    {
        $this->post->ID           = $this->schemaObject['@id'];
        $this->post->post_title   = $this->schemaObject['title'];
        $this->post->post_content = $this->schemaObject['description'];
        $this->post->post_type    = 'event';

        return $this->post;
    }
}
