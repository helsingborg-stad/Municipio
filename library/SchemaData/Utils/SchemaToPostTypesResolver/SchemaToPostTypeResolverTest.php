<?php

namespace Municipio\SchemaData\Utils\SchemaToPostTypesResolver;

use AcfService\Contracts\GetField;
use PHPUnit\Framework\MockObject\MockObject;
use WpService\Contracts\PostTypeExists;
use PHPUnit\Framework\TestCase;

class SchemaToPostTypeResolverTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated(): void
    {
        $resolver = $this->createResolver();
        $this->assertInstanceOf(SchemaToPostTypeResolver::class, $resolver);
    }

    /**
     * @testdox resolve method returns an empty array for any schema type
     */
    public function testResolveReturnsEmptyArray(): void
    {
        $resolver = $this->createResolver();
        $result   = $resolver->resolve('unknown_schema_type');
        $this->assertEmpty(iterator_to_array($result));
    }

    /**
     * @testdox resolve method returns an array of post types connected to the schema type
     */
    public function testResolveReturnsPostTypes(): void
    {
        $acfServiceMock = $this->getAcfServiceMock();
        $wpServiceMock  = $this->getWpServiceMock();

        $acfServiceMock->expects($this->once())
            ->method('getField')
            ->with('post_type_schema_types', 'option')
            ->willReturn([
                ['post_type' => 'post', 'schema_type' => 'connected_schema_type'],
                ['post_type' => 'page', 'schema_type' => 'connected_schema_type'],
            ]);
        $wpServiceMock->method('postTypeExists')->willReturn(true);

        $resolver = new SchemaToPostTypeResolver($acfServiceMock, $wpServiceMock);
        $result   = $resolver->resolve('connected_schema_type');

        $this->assertNotEmpty($result);
        $this->assertContains('post', $result);
        $this->assertContains('page', $result);
    }

    private function createResolver(): SchemaToPostTypeResolver
    {
        return new SchemaToPostTypeResolver(
            $this->getAcfServiceMock(),
            $this->getWpServiceMock()
        );
    }

    /**
     * @return GetField&MockObject
     */
    private function getAcfServiceMock(): GetField|MockObject
    {
        return $this->createMock(GetField::class);
    }

    /**
     * @return PostTypeExists&MockObject
     */
    private function getWpServiceMock(): PostTypeExists|MockObject
    {
        return $this->createMock(PostTypeExists::class);
    }
}
