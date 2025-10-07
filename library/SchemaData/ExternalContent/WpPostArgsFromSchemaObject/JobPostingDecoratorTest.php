<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\SchemaData\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\JobPosting;

class JobPostingDecoratorTest extends TestCase
{
    /**
     * @testdox Sets title from schemaObject['title'] if is JobPosting
     */
    public function testSetsTitleFromNameIfTitleIsMissing()
    {
        $factory      = new JobPostingDecorator(new WpPostArgsFromSchemaObject());
        $schemaObject = new JobPosting();

        $schemaObject->title('Job title');
        $post = $factory->transform($schemaObject);

        $this->assertEquals('Job title', $post['post_title']);
    }
}
