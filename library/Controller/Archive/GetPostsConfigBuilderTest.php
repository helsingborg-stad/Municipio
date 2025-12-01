<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetPostsConfigBuilderTest extends TestCase
{
    #[TestDox('It should build a GetPostsConfigInterface with the correct properties')]
    public function testBuild(): void
    {
        $config = (new GetPostsConfigBuilder())
            ->setPostTypes(['post', 'page'])
            ->setFacettingEnabled(true)
            ->setOrderBy('title')
            ->setOrder(OrderDirection::ASC)
            ->setPerPage(20)
            ->setDateSource('modified')
            ->setTerms(['category' => [1, 2, 3]])
            ->setCurrentPage(3)
            ->setSearch('test search')
            ->setDateFrom('2023-01-01')
            ->setDateTo('2023-12-31')
            ->build();

        $this->assertInstanceOf(GetPostsConfigInterface::class, $config);
        $this->assertEquals(['post', 'page'], $config->getPostTypes());
        $this->assertTrue($config->isFacettingTaxonomyQueryEnabled());
        $this->assertEquals('title', $config->getOrderBy());
        $this->assertEquals(OrderDirection::ASC, $config->getOrder());
        $this->assertEquals(20, $config->getPostsPerPage());
        $this->assertEquals('modified', $config->getDateSource());
        $this->assertEquals(['category' => [1, 2, 3]], $config->getTerms());
        $this->assertEquals(3, $config->getPage());
        $this->assertEquals('test search', $config->getSearch());
        $this->assertEquals('2023-01-01', $config->getDateFrom());
        $this->assertEquals('2023-12-31', $config->getDateTo());
    }
}
