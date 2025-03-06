<?php

namespace Municipio\ExternalContent\Taxonomy;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\Config\SourceTaxonomyConfigInterface;
use PHPUnit\Framework\TestCase;
use WP_Taxonomy;
use WpService\Contracts\RegisterTaxonomy;

class RegisterTaxonomiesFromSourceConfigTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $sourceConfig = $this->createMock(SourceConfigInterface::class);
        $wpService    = $this->createMock(RegisterTaxonomy::class);

        $registerTaxonomiesFromSourceConfig = new RegisterTaxonomiesFromSourceConfig($sourceConfig, $wpService);

        $this->assertInstanceOf(RegisterTaxonomiesFromSourceConfig::class, $registerTaxonomiesFromSourceConfig);
    }

        /**
     * @testdox taxonomies are registered
     */
    public function testRegisterTaxonomies()
    {
        $taxonomyConfig = $this->createMock(SourceTaxonomyConfigInterface::class);
        $taxonomyConfig->method('getName')->willReturn('custom_taxonomy');
        $taxonomyConfig->method('getPluralName')->willReturn('Custom Taxonomies');
        $taxonomyConfig->method('getSingularName')->willReturn('Custom Taxonomy');
        $taxonomyConfig->method('isHierarchical')->willReturn(true);

        $sourceConfig = $this->createMock(SourceConfigInterface::class);
        $sourceConfig->method('getTaxonomies')->willReturn([$taxonomyConfig]);
        $sourceConfig->method('getPostType')->willReturn('custom_post_type');

        $wpService = $this->createMock(RegisterTaxonomy::class);
        $wpService->expects($this->once())
            ->method('registerTaxonomy')
            ->with(
                'custom_taxonomy',
                'custom_post_type',
                [
                    'labels'            => [
                        'name'          => 'Custom Taxonomies',
                        'singular_name' => 'Custom Taxonomy',
                    ],
                    'hierarchical'      => true,
                    'show_ui'           => true,
                    'show_admin_column' => true,
                    'query_var'         => true,
                    'rewrite'           => ['slug' => 'custom_taxonomy']
                ]
            )->willReturn(new WP_Taxonomy('custom_taxonomy', 'custom_post_type'));

        $registerTaxonomiesFromSourceConfig = new RegisterTaxonomiesFromSourceConfig($sourceConfig, $wpService);
        $registerTaxonomiesFromSourceConfig->registerTaxonomies();
    }
}
