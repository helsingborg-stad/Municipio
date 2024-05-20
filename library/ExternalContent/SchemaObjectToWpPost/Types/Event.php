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
        $this->post->ID           = $this->schemaObject->getProperty('identifier');
        $this->post->post_title   = $this->schemaObject->getProperty('name');
        $this->post->post_content = $this->schemaObject->getProperty('description');
        $this->post->post_type    = 'event';

        return $this->post;
    }
}
