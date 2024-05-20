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
        $this->post->ID           = $this->schemaObject['@id'];
        $this->post->post_title   = $this->schemaObject['title'];
        $this->post->post_content = $this->schemaObject['description'];
        $this->post->post_type    = 'jobposting';

        return $this->post;
    }
}
