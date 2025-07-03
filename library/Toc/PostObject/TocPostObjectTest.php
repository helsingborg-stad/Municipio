<?php

namespace Municipio\Toc\PostObject;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Toc\Utils\TocUtilsInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TocPostObjectTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $wpService  = new FakeWpService([]);
        $tocUtils   = $this->createMock(TocUtilsInterface::class);

        $tocPostObject = new TocPostObject($postObject, $wpService, $tocUtils);

        $this->assertInstanceOf(TocPostObject::class, $tocPostObject);
    }

    /**
     * @testdox getTableOfContents returns TOC data from utils
     */
    public function testGetTableOfContentsReturnsTocDataFromUtils(): void
    {
        $expectedToc = [['label' => 'Test Heading', 'level' => 2, 'href' => '#test-heading', 'children' => []]];

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn('<h2>Test Heading</h2>');

        $tocUtils = $this->createMock(TocUtilsInterface::class);
        $tocUtils->method('getTableOfContents')->willReturn($expectedToc);

        $wpService = new FakeWpService([]);

        $tocPostObject = new TocPostObject($postObject, $wpService, $tocUtils);
        $result        = $tocPostObject->getTableOfContents();

        $this->assertEquals($expectedToc, $result);
    }

    /**
     * @testdox hasTableOfContents returns true when TOC exists
     */
    public function testHasTableOfContentsReturnsTrueWhenTocExists(): void
    {
        $expectedToc = [['label' => 'Test Heading', 'level' => 2, 'href' => '#test-heading', 'children' => []]];

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn('<h2>Test Heading</h2>');

        $tocUtils = $this->createMock(TocUtilsInterface::class);
        $tocUtils->method('getTableOfContents')->willReturn($expectedToc);

        $wpService = new FakeWpService([]);

        $tocPostObject = new TocPostObject($postObject, $wpService, $tocUtils);
        $result        = $tocPostObject->hasTableOfContents();

        $this->assertTrue($result);
    }

    /**
     * @testdox hasTableOfContents returns false when no TOC exists
     */
    public function testHasTableOfContentsReturnsFalseWhenNoTocExists(): void
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn('<p>No headings here</p>');

        $tocUtils = $this->createMock(TocUtilsInterface::class);
        $tocUtils->method('getTableOfContents')->willReturn([]);

        $wpService = new FakeWpService([]);

        $tocPostObject = new TocPostObject($postObject, $wpService, $tocUtils);
        $result        = $tocPostObject->hasTableOfContents();

        $this->assertFalse($result);
    }

    /**
     * @testdox getContentHeadings returns same data as getTableOfContents
     */
    public function testGetContentHeadingsReturnsSameDataAsGetTableOfContents(): void
    {
        $expectedToc = [['label' => 'Test Heading', 'level' => 2, 'href' => '#test-heading', 'children' => []]];

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn('<h2>Test Heading</h2>');

        $tocUtils = $this->createMock(TocUtilsInterface::class);
        $tocUtils->method('getTableOfContents')->willReturn($expectedToc);

        $wpService = new FakeWpService([]);

        $tocPostObject = new TocPostObject($postObject, $wpService, $tocUtils);

        $this->assertEquals($expectedToc, $tocPostObject->getContentHeadings());
        $this->assertEquals($tocPostObject->getTableOfContents(), $tocPostObject->getContentHeadings());
    }
}
