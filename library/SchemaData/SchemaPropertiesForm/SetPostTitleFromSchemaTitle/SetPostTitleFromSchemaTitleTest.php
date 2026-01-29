<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\SetPostTitleFromSchemaTitle;

use Municipio\Schema\Schema;
use Municipio\SchemaData\Helper\GetSchemaType;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use PHPUnit\Framework\MockObject\MockObject;
use WP_Post;
use WpService\Contracts\WpUpdatePost;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class SetPostTitleFromSchemaTitleTest extends \PHPUnit\Framework\TestCase
{
    private SchemaObjectFromPostInterface|MockObject $schemaObjectFromPost;
    private WpService $wpService;
    private SetPostTitleFromSchemaTitle $instance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schemaObjectFromPost = $this->getSchemaFactoryMock();
        $this->wpService = $this->getWpService();
        $this->instance = new SetPostTitleFromSchemaTitle($this->schemaObjectFromPost, $this->wpService);
    }

    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(SetPostTitleFromSchemaTitle::class, $this->instance);
    }

    #[TestDox('addHooks attaches to the \'save_post\' action')]
    public function testAddHooks()
    {
        $this->instance->addHooks();
        $this->assertEquals('save_post', $this->wpService->methodCalls['addAction'][0][0]);
    }

    #[TestDox('setPostTitleFromSchemaTitle() does not update post if schema name is empty')]
    public function testSetPostTitleFromSchemaTitleDoesNotUpdatePostIfSchemaNameIsEmpty()
    {
        $post = new WP_Post([]);
        $post->ID = 1;
        $schemaObject = Schema::thing();
        $this->schemaObjectFromPost->method('create')->willReturn($schemaObject);

        $this->instance->setPostTitleFromSchemaTitle($post->ID, $post);

        $this->assertArrayNotHasKey('wpUpdatePost', $this->wpService->methodCalls);
    }

    #[TestDox('setPostTitleFromSchemaTitle() updates post title using schema name if it is not empty and is not the same as the current post title.')]
    public function testSetPostTitleFromSchemaTitleUpdatesPostIfSchemaNameIsNotEmpty()
    {
        $post = new WP_Post([]);
        $post->ID = 1;
        $post->post_title = 'Title from Post';
        $schemaObject = Schema::thing();
        $schemaObject->setProperty('name', 'Title from Schema');
        $this->schemaObjectFromPost->method('create')->willReturn($schemaObject);
        $this->instance->method('hasActiveSchemaType')->willReturn(true);

        $this->instance->setPostTitleFromSchemaTitle($post->ID, $post);

        $this->assertArrayHasKey('wpUpdatePost', $this->wpService->methodCalls);
        $this->assertEquals('Title from Schema', $this->wpService->methodCalls['wpUpdatePost'][0][0]['post_title']);
    }

    #[TestDox('setPostTitleFromSchemaTitle() does not update post title if schema name is the same as the current post title.')]
    public function testSetPostTitleFromSchemaTitleDoesNotUpdatePostIfSchemaNameIsSameAsPostTitle()
    {
        $title = 'Title from Post';
        $post = new WP_Post([]);
        $post->ID = 1;
        $post->post_title = $title;
        $schemaObject = Schema::thing();
        $schemaObject->setProperty('name', $title);
        $this->schemaObjectFromPost->method('create')->willReturn($schemaObject);
        $instance = $this->createMock(SetPostTitleFromSchemaTitle::class);
        $instance->method('hasActiveSchemaType')->willReturn('Schema');
        $instance->setPostTitleFromSchemaTitle($post->ID, $post);

        $this->assertArrayNotHasKey('wpUpdatePost', $this->wpService->methodCalls);
    }

    private function getSchemaFactoryMock(): SchemaObjectFromPostInterface|MockObject
    {
        return $this->createMock(SchemaObjectFromPostInterface::class);
    }

    private function getWpService(): WpService
    {
        return new FakeWpService([
            'addAction' => true,
            'wpUpdatePost' => true,
        ]);
    }
}
