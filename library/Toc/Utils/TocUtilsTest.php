<?php

namespace Municipio\Toc\Utils;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TocUtilsTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $wpService = new FakeWpService([]);
        $tocUtils  = new TocUtils($wpService, new \AcfService\Implementations\FakeAcfService());

        $this->assertInstanceOf(TocUtils::class, $tocUtils);
    }

    /**
     * @testdox shouldEnableToc returns false when not on singular page
     */
    public function testShouldEnableTocReturnsFalseWhenNotOnSingularPage(): void
    {
        $wpService  = new FakeWpService(['isSingular' => false]);
        $postObject = $this->createMock(PostObjectInterface::class);

        $tocUtils = new TocUtils($wpService, new \AcfService\Implementations\FakeAcfService());
        $result   = $tocUtils->shouldEnableToc($postObject);

        $this->assertFalse($result);
    }

    /**
     * @testdox shouldEnableToc returns false when content is empty
     */
    public function testShouldEnableTocReturnsFalseWhenContentIsEmpty(): void
    {
        $wpService  = new FakeWpService(['isSingular' => true]);
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn('');

        $tocUtils = new TocUtils($wpService, new \AcfService\Implementations\FakeAcfService());
        $result   = $tocUtils->shouldEnableToc($postObject);

        $this->assertFalse($result);
    }

    /**
     * @testdox shouldEnableToc returns false when content has no headings
     */
    public function testShouldEnableTocReturnsFalseWhenContentHasNoHeadings(): void
    {
        $wpService  = new FakeWpService(['isSingular' => true]);
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn('<p>Just some paragraph text with no headings.</p>');

        $tocUtils = new TocUtils($wpService, new \AcfService\Implementations\FakeAcfService());
        $result   = $tocUtils->shouldEnableToc($postObject);

        $this->assertFalse($result);
    }

    /**
     * @testdox shouldEnableToc returns true when content has minimum reqired headings
     */
    public function testShouldEnableTocReturnsTrueWhenContentHasHeadings(): void
    {
        $wpService  = new FakeWpService(['isSingular' => true]);
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn('<h2>A heading</h2><h2>A heading</h2><h2>A heading</h2><h2>A heading</h2><p>Some content</p>');

        $tocUtils = new TocUtils($wpService, new \AcfService\Implementations\FakeAcfService(
            ['getField' => true]
        ));
        $result   = $tocUtils->shouldEnableToc($postObject);

        $this->assertTrue($result);
    }

    /**
     * @testdox getTableOfContents returns empty array for empty content
     */
    public function testGetTableOfContentsReturnsEmptyArrayForEmptyContent(): void
    {
        $wpService = new FakeWpService([]);
        $tocUtils  = new TocUtils($wpService, new \AcfService\Implementations\FakeAcfService());

        $result = $tocUtils->getTableOfContents('');

        $this->assertEquals([], $result);
    }

    /**
     * @testdox getContentWithAnchors returns original content when empty
     */
    public function testGetContentWithAnchorsReturnsOriginalContentWhenEmpty(): void
    {
        $wpService = new FakeWpService([]);
        $tocUtils  = new TocUtils($wpService, new \AcfService\Implementations\FakeAcfService());

        $result = $tocUtils->getContentWithAnchors('');

        $this->assertEquals('', $result);
    }
}
