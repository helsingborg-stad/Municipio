<?php

namespace Municipio\PostsList\GetPosts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class WpQueryFactoryTest extends TestCase
{
    #[TestDox('create returns WP_Query instance')]
    public function testCreateReturnsWpQueryInstance(): void
    {
        $wpQuery = WpQueryFactory::create([]);
        $this->assertInstanceOf(\WP_Query::class, $wpQuery);
    }
}
