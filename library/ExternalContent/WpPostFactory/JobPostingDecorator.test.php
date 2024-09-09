<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\JobPosting;

class JobPostingDecoratorTest extends TestCase
{
    /**
     * @testdox Sets title from schemaObject['title'] if is JobPosting
     */
    public function testSetsTitleFromNameIfTitleIsMissing()
    {
        $factory      = new JobPostingDecorator(new WpPostFactory());
        $schemaObject = new JobPosting();

        $schemaObject->title('Job title');
        $post = $factory->create($schemaObject, new Source('', ''));

        $this->assertEquals('Job title', $post['post_title']);
    }
}
