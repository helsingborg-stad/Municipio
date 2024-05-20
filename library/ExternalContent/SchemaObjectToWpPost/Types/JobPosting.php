<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost\Types;

use Municipio\ExternalContent\SchemaObjectToWpPost\ISchemaObjectToWpPost;
use Spatie\SchemaOrg\JobPosting as JobPostingSchema;
use WP_Post;

class JobPosting implements ISchemaObjectToWpPost
{
    private WP_Post $post;

    public function __construct(private JobPostingSchema $schemaObject)
    {
        $this->post = new WP_Post((object)[]);
    }

    public function toWpPost(): WP_Post
    {
        $this->post->ID           = $this->schemaObject->getProperty('identifier');
        $this->post->post_title   = $this->schemaObject->getProperty('title');
        $this->post->post_content = $this->schemaObject->getProperty('description');
        $this->post->post_type    = 'jobposting';

        return $this->post;
    }
}
