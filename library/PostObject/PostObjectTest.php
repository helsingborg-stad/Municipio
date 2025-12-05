<?php

namespace Municipio\PostObject;

use PHPUnit\Framework\TestCase;
use WP_Error;
use WP_Term;
use WpService\Implementations\FakeWpService;

/**
 * PostObject
 */
class PostObjectTest extends TestCase
{
    private PostObject $instance;

    protected function setUp(): void
    {
        $this->instance = new PostObject(1, new FakeWpService([ 'getCurrentBlogId' => 1 ]));
    }

    #[TestDox('getId() returns provided id')]
    public function testGetIdReturns0()
    {
        $this->assertEquals(1, $this->instance->getId());
    }

    #[TestDox('getTitle() returns an empty string')]
    public function testGetTitleReturnsAnEmptyString()
    {
        $this->assertEquals('', $this->instance->getTitle());
    }

    #[TestDox('getPermalink() returns an empty string')]
    public function testGetPermalinkReturnsAnEmptyString()
    {
        $this->assertEquals('', $this->instance->getPermalink());
    }

    #[TestDox('getCommentCount() returns 0')]
    public function testGetCommentCountReturns0()
    {
        $this->assertEquals(0, $this->instance->getCommentCount());
    }

    #[TestDox('getPostType() returns an empty string')]
    public function testGetPostTypeReturnsAnEmptyString()
    {
        $this->assertEquals('', $this->instance->getPostType());
    }

    #[TestDox('getIcon() returns null')]
    public function testGetIconReturnsNull()
    {
        $this->assertNull($this->instance->getIcon());
    }

    #[TestDox('getBlogId() current blog id')]
    public function testGetBlogIdReturns1()
    {
        $this->assertEquals(1, $this->instance->getBlogId());
    }

    #[TestDox('getArchiveDateTimestamp() returns 0')]
    public function testGetArchiveDateTimestampReturnsNull()
    {
        $this->assertEquals(null, $this->instance->getArchiveDateTimestamp());
    }

    #[TestDox('getArchiveDateFormat() returns default format')]
    public function testGetArchiveDateFormatReturnsDefaultFormat()
    {
        $this->assertEquals('date-time', $this->instance->getArchiveDateFormat());
    }

    #[TestDox('getSchemaProperty() returns null')]
    public function testGetSchemaPropertyReturnsNull()
    {
        $this->assertEquals(null, $this->instance->getSchemaProperty('non_existing_property'));
    }

    #[TestDox('getTerms returns an empty array if terms could not be retrieved')]
    public function testGetTermsReturnsEmptyArray()
    {
        $this->instance = new PostObject(1, new FakeWpService([ 'wpGetPostTerms' => new WP_Error(), ]));

        $this->assertEquals([], $this->instance->getTerms(['category']));
    }

    #[TestDox('getTerms returns an empty array if no terms are found')]
    public function testGetTermsReturnsEmptyArrayIfNoTermsFound()
    {
        $this->instance = new PostObject(1, new FakeWpService([ 'wpGetPostTerms' => [], ]));

        $this->assertEquals([], $this->instance->getTerms(['category']));
    }

    #[TestDox('getTerms returns an array of terms')]
    public function testGetTermsReturnsArrayOfTerms()
    {
        $terms = [ new WP_Term([]), ];

        $this->instance = new PostObject(1, new FakeWpService([ 'wpGetPostTerms' => $terms, ]));

        $this->assertEquals($terms, $this->instance->getTerms(['category']));
    }

    #[TestDox('__get() returns null')]
    public function testMagicGetReturnsNull()
    {
        $this->assertEquals(null, $this->instance->non_existing_property);
    }
}
