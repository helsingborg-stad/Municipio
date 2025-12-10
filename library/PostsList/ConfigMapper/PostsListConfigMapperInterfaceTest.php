<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use PHPUnit\Framework\TestCase;

class PostsListConfigMapperInterfaceTest extends TestCase
{
    public function testInterfaceMethodExists(): void
    {
        $reflection = new \ReflectionClass(PostsListConfigMapperInterface::class);
        $this->assertTrue($reflection->hasMethod('map'));
        $method = $reflection->getMethod('map');
        $this->assertTrue($method->isPublic());
    }
}
