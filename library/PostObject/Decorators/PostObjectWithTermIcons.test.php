<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\TermIcon\TryGetTermIconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\TermIcon\TermIconInterface;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetObjectTaxonomies;
use WpService\Contracts\GetTheTerms;
use WpService\Implementations\FakeWpService;

class PostObjectWithTermIconsTest extends TestCase
{
    /**
     * @testdox getTermIcons() returns an empty array if the post object has no terms.
     */
    public function testGetTermIconsReturnsEmptyArrayIfPostObjectHasNoTerms()
    {
        $wpService  = new FakeWpService(['getObjectTaxonomies' => ['category'], 'getTheTerms' => []]);
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getId')->willReturn(1);
        $postObject->method('getPostType')->willReturn('post');

        $tryGetTermIcon = $this->createMock(TryGetTermIconInterface::class);

        $decorator = new PostObjectWithTermIcons($postObject, $wpService, $tryGetTermIcon);

        $this->assertEquals([], $decorator->getTermIcons());
    }

    /**
     * @testdox getTermIcons() returns an array of term icons if the post object has terms.
     */
    public function testGetTermIconsReturnsArrayOfTermIconsIfPostObjectHasTerms()
    {
        $wpService = new FakeWpService([
            'getObjectTaxonomies' => ['category'],
            'getTheTerms'         => [
                (object) ['term_id' => 1],
                (object) ['term_id' => 2],
            ],
        ]);

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getId')->willReturn(1);
        $postObject->method('getPostType')->willReturn('post');

        $tryGetTermIcon = $this->createMock(TryGetTermIconInterface::class);
        $tryGetTermIcon->method('tryGetTermIcon')->willReturn($this->createMock(TermIconInterface::class));

        $decorator = new PostObjectWithTermIcons($postObject, $wpService, $tryGetTermIcon);

        $this->assertIsArray($decorator->getTermIcons());
        $this->assertContainsOnlyInstancesOf(TermIconInterface::class, $decorator->getTermIcons());
    }

    /**
     * @testdox getTermIcon() returns null if the post object has no terms.
     */
    public function testGetTermIconReturnsNullIfPostObjectHasNoTerms()
    {
        $wpService  = new FakeWpService(['getObjectTaxonomies' => ['category'], 'getTheTerms' => []]);
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getId')->willReturn(1);
        $postObject->method('getPostType')->willReturn('post');

        $tryGetTermIcon = $this->createMock(TryGetTermIconInterface::class);

        $decorator = new PostObjectWithTermIcons($postObject, $wpService, $tryGetTermIcon);

        $this->assertNull($decorator->getTermIcon());
    }

    /**
     * @testdox getTermIcon() returns a single term icon if the post object has terms.
     */
    public function testGetTermIconReturnsSingleTermIconIfPostObjectHasTerms()
    {
        $wpService = new FakeWpService([
            'getObjectTaxonomies' => ['category'],
            'getTheTerms'         => [
                (object) ['term_id' => 1],
                (object) ['term_id' => 2],
            ],
        ]);

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getId')->willReturn(1);
        $postObject->method('getPostType')->willReturn('post');

        $tryGetTermIcon = $this->createMock(TryGetTermIconInterface::class);
        $tryGetTermIcon->method('tryGetTermIcon')->willReturn($this->createMock(TermIconInterface::class));

        $decorator = new PostObjectWithTermIcons($postObject, $wpService, $tryGetTermIcon);

        $this->assertInstanceOf(TermIconInterface::class, $decorator->getTermIcon());
    }

    /**
     * @testdox getTermIcon() returns a term icon for a specific taxonomy if the post object has terms.
     */
    public function testGetTermIconReturnsTermIconForSpecificTaxonomyIfPostObjectHasTerms()
    {
        $term           = $this->getMockBuilder(\stdClass::class)->setMockClassName('WP_Term')->getMock();
        $term->taxonomy = 'category';
        $wpService      = new FakeWpService([
            'getObjectTaxonomies' => ['category'],
            'getTheTerms'         => [
                (object) ['term_id' => 1],
                (object) ['term_id' => 2],
            ],
            'getTerm'             => $term
        ]);

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getId')->willReturn(1);
        $postObject->method('getPostType')->willReturn('post');

        $tryGetTermIcon = $this->createMock(TryGetTermIconInterface::class);
        $tryGetTermIcon->method('tryGetTermIcon')->willReturn($this->createMock(TermIconInterface::class));

        $decorator = new PostObjectWithTermIcons($postObject, $wpService, $tryGetTermIcon);

        $this->assertInstanceOf(TermIconInterface::class, $decorator->getTermIcon('category'));
    }
}
