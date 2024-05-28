<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\Services\NullSourceService;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\JobPosting;

class WpPostFactoryJobPostingDecoratorTest extends TestCase
{
    /**
     * @testdox Sets title from schemaObject['title'] if is JobPosting
     */
    public function testSetsTitleFromNameIfTitleIsMissing()
    {
        $factory      = new WpPostFactoryJobPostingDecorator(new WpPostFactory());
        $schemaObject = new JobPosting();

        $schemaObject->title('Job title');
        $post = $factory->create($schemaObject, new NullSourceService());

        $this->assertEquals('Job title', $post->post_title);
    }
}
