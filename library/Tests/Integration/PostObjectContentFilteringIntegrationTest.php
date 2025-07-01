<?php

/**
 * Integration test to verify the PostObject content filtering works end-to-end
 */

namespace Municipio\Tests\Integration;

use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\PostObject\Decorators\PostObjectWithFilteredContent;
use Municipio\Toc\PostObject\TocPostObject;
use Municipio\Toc\Utils\TocUtils;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WpService\Implementations\FakeWpService;
use AcfService\Implementations\FakeAcfService;

class PostObjectContentFilteringIntegrationTest extends TestCase
{
    /**
     * @testdox PostObject creation pipeline includes content filtering
     * @group integration
     */
    public function testPostObjectCreationPipelineIncludesContentFiltering(): void
    {
        // Create a fake WP_Post with some content
        $wpPost = new WP_Post((object)[
            'ID' => 123,
            'post_title' => 'Test Post',
            'post_content' => '<h2>Heading</h2><p>Content with heading</p>',
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_date' => '2023-01-01 00:00:00',
            'post_date_gmt' => '2023-01-01 00:00:00',
            'post_modified' => '2023-01-01 00:00:00',
            'post_modified_gmt' => '2023-01-01 00:00:00'
        ]);

        // Create services
        $wpService = new FakeWpService([
            'applyFilters' => function($hook, $content, ...$args) {
                if ($hook === 'the_content') {
                    return '<div class="filtered-content">' . $content . '</div>';
                }
                if ($hook === 'the_title') {
                    return 'Filtered: ' . $content;
                }
                return $content;
            }
        ]);

        $acfService = new FakeAcfService([]);
        
        $schemaObjectFromPost = $this->createMock(\Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface::class);

        // Create the factory
        $factory = new CreatePostObjectFromWpPost($wpService, $acfService, $schemaObjectFromPost);

        // Create PostObject - this should include the PostObjectWithFilteredContent decorator
        $postObject = $factory->create($wpPost);

        // Verify that content and title are filtered
        $this->assertStringContains('filtered-content', $postObject->getContent());
        $this->assertStringContains('Filtered: Test Post', $postObject->getTitle());
        
        // Verify the original methods still work
        $this->assertEquals(123, $postObject->getId());
    }

    /**
     * @testdox TOC PostObject works with filtered content
     * @group integration
     */
    public function testTocPostObjectWorksWithFilteredContent(): void
    {
        // Create a base PostObject that returns filtered content
        $basePostObject = $this->createMock(\Municipio\PostObject\PostObjectInterface::class);
        $basePostObject->method('getContent')->willReturn('<h2>Test Heading</h2><p>Some content</p>');

        $wpService = new FakeWpService([]);
        $tocUtils = new TocUtils();

        // Create TOC PostObject
        $tocPostObject = new TocPostObject($basePostObject, $wpService, $tocUtils);

        // Verify TOC works with the filtered content
        $tableOfContents = $tocPostObject->getTableOfContents();
        
        $this->assertNotEmpty($tableOfContents);
        $this->assertEquals('Test Heading', $tableOfContents[0]['label']);
        $this->assertEquals(2, $tableOfContents[0]['level']);

        // Verify content is enhanced with anchors
        $content = $tocPostObject->getContent();
        $this->assertStringContains('id="test-heading"', $content);
    }
}