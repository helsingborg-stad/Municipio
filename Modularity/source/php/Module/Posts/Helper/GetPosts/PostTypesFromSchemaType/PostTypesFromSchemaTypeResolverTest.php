<?php

namespace Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class PostTypesFromSchemaTypeResolverTest extends TestCase {
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void {
        $resolver = new PostTypesFromSchemaTypeResolver();

        $this->assertInstanceOf(PostTypesFromSchemaTypeResolver::class, $resolver);
    }

    #[RunInSeparateProcess]
    #[TestDox('returns the correct post type for a given schema type')]
    public function testResolveReturnsCorrectPostType(): void {

        // Mock the Municipio\SchemaData\Helper\GetSchemaType class
        if (!class_exists('\Municipio\SchemaData\Helper\GetSchemaType', false)) {
            eval('
                namespace Municipio\SchemaData\Helper;
                class GetSchemaType {
                    public function getPostTypesFromSchemaType($schemaType) {
                        return $schemaType === "JobPosting" ? ["custom_post_type"] : [];
                    }
                }
            ');
        }

        $resolver = new PostTypesFromSchemaTypeResolver();

        $this->assertSame(['custom_post_type'], $resolver->resolve('JobPosting'));
    }

    #[TestDox('uses next resolver if unavailable')]
    public function testUsesNextResolverIfUnavailable(): void {
        $nextResolver = $this->createMock(PostTypesFromSchemaTypeResolverInterface::class);
        $nextResolver->method('resolve')->willReturn(['post-type-from-next-resolver']);

        $resolver = new PostTypesFromSchemaTypeResolver($nextResolver);

        $this->assertSame(['post-type-from-next-resolver'], $resolver->resolve('JobPosting'));
    }
}