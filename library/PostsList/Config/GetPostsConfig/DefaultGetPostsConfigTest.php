<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DefaultGetPostsConfigTest extends TestCase
{
    #[TestDox('getPostTypes contains only "post" by default')]
    public function testGetPostTypes(): void
    {
        $config = new DefaultGetPostsConfig();
        $this->assertEquals(['post'], $config->getPostTypes());
    }

    #[TestDox('getPostsPerPage returns 10 by default')]
    public function testGetPostsPerPage(): void
    {
        $config = new DefaultGetPostsConfig();
        $this->assertEquals(10, $config->getPostsPerPage());
    }

    #[TestDox('isFacettingTaxonomyQueryEnabled returns false by default')]
    public function testIsFacettingTaxonomyQueryEnabled(): void
    {
        $config = new DefaultGetPostsConfig();
        $this->assertFalse($config->isFacettingTaxonomyQueryEnabled());
    }

    #[TestDox('getSearch returns null by default')]
    public function testGetSearch(): void
    {
        $config = new DefaultGetPostsConfig();
        $this->assertNull($config->getSearch());
    }

    #[TestDox('getDateFrom returns null by default')]
    public function testGetDateFrom(): void
    {
        $config = new DefaultGetPostsConfig();
        $this->assertNull($config->getDateFrom());
    }

    #[TestDox('getDateTo returns null by default')]
    public function testGetDateTo(): void
    {
        $config = new DefaultGetPostsConfig();
        $this->assertNull($config->getDateTo());
    }

    #[TestDox('getTerms returns an empty array by default')]
    public function testGetTerms(): void
    {
        $config = new DefaultGetPostsConfig();
        $this->assertEquals([], $config->getTerms());
    }
}
