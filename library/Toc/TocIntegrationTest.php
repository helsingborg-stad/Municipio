<?php

namespace Municipio\Toc;

use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Toc\PostObject\TocPostObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TocIntegrationTest extends TestCase
{
    /**
     * @testdox TocFeature decorates post objects with TOC functionality
     */
    public function testTocFeatureDecoratesPostObjectsWithTocFunctionality(): void
    {
        $wpService = new FakeWpService(['addFilter' => true, 'isSingular' => true]);
        $tocFeature = new TocFeature($wpService);
        
        // Enable the feature
        $tocFeature->enable();
        
        // Verify that the filter was registered
        $this->assertArrayHasKey('addFilter', $wpService->methodCalls);
        $this->assertCount(1, $wpService->methodCalls['addFilter']);
        
        $filterCall = $wpService->methodCalls['addFilter'][0];
        $this->assertEquals(CreatePostObjectFromWpPost::DECORATE_FILTER_NAME, $filterCall[0]);
        $this->assertIsCallable($filterCall[1]);
    }

    /**
     * @testdox TocFeature only decorates posts when TOC should be enabled
     */
    public function testTocFeatureOnlyDecoratesPostsWhenTocShouldBeEnabled(): void
    {
        $wpService = new FakeWpService(['addFilter' => true, 'isSingular' => false]);
        $tocFeature = new TocFeature($wpService);
        $tocFeature->enable();
        
        // Get the filter callback
        $filterCallback = $wpService->methodCalls['addFilter'][0][1];
        
        // Create a mock post object without content
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn('');
        
        // The decorator should not be applied since there's no content
        $result = $filterCallback($postObject);
        
        $this->assertSame($postObject, $result);
        $this->assertNotInstanceOf(TocPostObject::class, $result);
    }

    /**
     * @testdox TocFeature decorates posts when they have headings
     */
    public function testTocFeatureDecoratesPostsWhenTheyHaveHeadings(): void
    {
        $wpService = new FakeWpService(['addFilter' => true, 'isSingular' => true]);
        $tocFeature = new TocFeature($wpService);
        $tocFeature->enable();
        
        // Get the filter callback
        $filterCallback = $wpService->methodCalls['addFilter'][0][1];
        
        // Create a mock post object with heading content
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn('<h2>Test Heading</h2><p>Content</p>');
        
        // The decorator should be applied since there are headings
        $result = $filterCallback($postObject);
        
        $this->assertInstanceOf(TocPostObject::class, $result);
        $this->assertNotSame($postObject, $result);
    }
}