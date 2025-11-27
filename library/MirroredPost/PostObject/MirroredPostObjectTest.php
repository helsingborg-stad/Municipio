<?php

declare(strict_types=1);

namespace Municipio\MirroredPost\PostObject;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;
use WpService\Implementations\FakeWpService;

class MirroredPostObjectTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $decorator = $this->createMirroredPostObject(1);
        static::assertInstanceOf(MirroredPostObject::class, $decorator);
    }

    #[TestDox('getBlogId returns the provided blog id')]
    public function testGetBlogIdReturnsTheProvidedBlogId(): void
    {
        $decorator = $this->createMirroredPostObject(2);
        static::assertSame(2, $decorator->getBlogId());
    }

    #[TestDox('getPermalink replaces the original site url with the current site url')]
    public function testGetPermalinkReplacesSiteUrl(): void
    {
        $postObject = $this->createPostObjectStub([
            'getPermalink' => 'http://other-site.com/hello-world/',
        ]);
        $wpService = $this->createWpService();
        $decorator = new MirroredPostObject($postObject, $wpService, 2);

        $permaLink = $decorator->getPermalink();

        static::assertSame(2, $wpService->methodCalls['switchToBlog'][0][0]);
        static::assertSame(1, $wpService->methodCalls['switchToBlog'][1][0]);
        static::assertSame('http://other-site.com/hello-world/', $permaLink);
    }

    #[TestDox('getIcon() switches to the blog using the provided blog id when getting the value')]
    public function testGetIconSwitchesToTheProvidedBlogIdWhenGettingTheValue(): void
    {
        $postObject = $this->createPostObjectStub(['getIcon' => null]);
        $wpService = $this->createWpService();
        $decorator = new MirroredPostObject($postObject, $wpService, 2);

        $icon = $decorator->getIcon();

        static::assertSame(2, $wpService->methodCalls['switchToBlog'][0][0]);
        static::assertSame(1, $wpService->methodCalls['switchToBlog'][1][0]);
        static::assertNull($icon);
    }

    #[TestDox('getSchemaProperty() switches to the blog using the provided blog id when getting the value')]
    public function testGetSchemaPropertySwitchesToTheProvidedBlogIdWhenGettingTheValue(): void
    {
        $postObject = $this->createPostObjectStub(['getSchemaProperty' => 'schema-value']);
        $wpService = $this->createWpService();
        $decorator = new MirroredPostObject($postObject, $wpService, 2);

        $schemaValue = $decorator->getSchemaProperty('some-property');

        static::assertSame(2, $wpService->methodCalls['switchToBlog'][0][0]);
        static::assertSame(1, $wpService->methodCalls['switchToBlog'][1][0]);
        static::assertSame('schema-value', $schemaValue);
    }

    #[TestDox('getSchema() switches to the blog using the provided blog id when getting the value')]
    public function testGetSchemaSwitchesToTheProvidedBlogIdWhenGettingTheValue(): void
    {
        $schema = Schema::thing();
        $postObject = $this->createPostObjectStub(['getSchema' => $schema]);
        $wpService = $this->createWpService();
        $decorator = new MirroredPostObject($postObject, $wpService, 2);

        $schemaData = $decorator->getSchema();

        static::assertCount(2, $wpService->methodCalls['switchToBlog']);
        static::assertSame($schema, $schemaData);
    }

    /**
     * Helper to create a MirroredPostObject with a stubbed PostObject and WpService.
     */
    private function createMirroredPostObject(int $blogId): MirroredPostObject
    {
        return new MirroredPostObject($this->createPostObjectStub(), $this->createWpService(), $blogId);
    }

    /**
     * Helper to create a stub for PostObjectInterface with optional method overrides.
     */
    private function createPostObjectStub(array $methods = []): PostObjectInterface|MockObject
    {
        $stub = $this->createStub(PostObjectInterface::class);
        foreach ($methods as $method => $return) {
            $stub->method($method)->willReturn($return);
        }
        return $stub;
    }

    /**
     * Helper to create a FakeWpService with default behaviors.
     */
    private function createWpService(): SwitchToBlog&RestoreCurrentBlog
    {
        return new FakeWpService([
            'switchToBlog' => true,
            'restoreCurrentBlog' => true,
            'getSiteUrl' => 'http://example.com',
            'addQueryArg' => static fn($args, $url) => $url . '?' . http_build_query($args),
            'getCurrentBlogId' => 1,
        ]);
    }
}
