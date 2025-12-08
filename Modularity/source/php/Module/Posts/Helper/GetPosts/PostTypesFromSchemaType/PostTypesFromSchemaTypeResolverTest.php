<?php

declare(strict_types=1);

namespace Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class PostTypesFromSchemaTypeResolverTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $resolver = new PostTypesFromSchemaTypeResolver();

        $this->assertInstanceOf(PostTypesFromSchemaTypeResolver::class, $resolver);
    }
}
