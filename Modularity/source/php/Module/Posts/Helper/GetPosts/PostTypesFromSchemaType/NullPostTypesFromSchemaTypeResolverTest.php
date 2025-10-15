<?php

namespace Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class NullPostTypesFromSchemaTypeResolverTest extends TestCase {
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void {
        $resolver = new NullPostTypesFromSchemaTypeResolver();

        $this->assertInstanceOf(NullPostTypesFromSchemaTypeResolver::class, $resolver);
    }
    
    #[TestDox('returns an empty array for any schema type')]
    public function testResolveReturnsEmptyArray(): void {
        $resolver = new NullPostTypesFromSchemaTypeResolver();

        $this->assertSame([], $resolver->resolve('JobPosting'));
        $this->assertSame([], $resolver->resolve('Article'));
        $this->assertSame([], $resolver->resolve('Event'));
    }
}