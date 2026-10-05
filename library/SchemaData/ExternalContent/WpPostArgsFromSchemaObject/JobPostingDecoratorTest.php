<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\JobPosting;
use Municipio\SchemaData\ExternalContent\Sources\Source;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class JobPostingDecoratorTest extends TestCase
{
    #[TestDox('Sets title from schemaObject[\'title\'] if is JobPosting')]
    public function testSetsTitleFromNameIfTitleIsMissing()
    {
        $factory = new JobPostingDecorator(new WpPostArgsFromSchemaObject());
        $schemaObject = new JobPosting();

        $schemaObject->title('Job title');
        $post = $factory->transform($schemaObject);

        $this->assertEquals('Job title', $post['post_title']);
    }

    #[TestDox('Sets post_date from schemaObject[\'datePosted\'] if is JobPosting')]
    public function testSetsPostDateFromDatePosted()
    {
        $factory = new JobPostingDecorator(new WpPostArgsFromSchemaObject());
        $schemaObject = new JobPosting();

        $schemaObject->datePosted(new \DateTime('2023-01-01 12:00:00'));
        $post = $factory->transform($schemaObject);

        $this->assertEquals('2023-01-01 12:00:00', $post['post_date']);
    }

    #[TestDox('Handles case when datePosted is a DateTimeInterface')]
    public function testHandlesDatePostedAsDateTimeInterface()
    {
        $factory = new JobPostingDecorator(new WpPostArgsFromSchemaObject());
        $schemaObject = new JobPosting();

        $schemaObject->datePosted(new \DateTime('2023-01-01 12:00:00'));
        $post = $factory->transform($schemaObject);

        $this->assertEquals('2023-01-01 12:00:00', $post['post_date']);
    }

    #[TestDox('Prepends employerOverview to post content if it exists in the schema object')]
    public function testPrependsEmployerOverviewToPostContent()
    {
        $factory = new JobPostingDecorator(new WpPostArgsFromSchemaObject());
        $schemaObject = new JobPosting();

        $schemaObject->employerOverview(['Employer overview content']);
        $post = $factory->transform($schemaObject);

        $this->assertStringContainsString('Employer overview content', $post['post_content']);
    }
}
