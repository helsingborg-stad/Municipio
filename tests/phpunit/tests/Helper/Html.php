<?php

namespace Municipio\Tests\Helper;

use Mockery;
use WP_Mock;
use WP_Mock\Tools\TestCase;
use Municipio\Helper\Html;

/**
 * Class HtmlTest
 * @group wp_mock
 */
class HtmlTest extends TestCase
{
    /**
     * @testdox createGridClass returns a grid class when given an int.
    */
    public function testCreateGridClassReturnsNormalGridClass()
    {
        // When
        $result = Html::createGridClass(2);

        // Then
        $this->assertEquals('o-grid-6', $result);
    }

    /**
     * @testdox createGridClass returns a grid class with a media query when given an int and string.
    */
    public function testCreateGridClassReturnsNormalGridClassWithMediaQuery()
    {
        // When
        $result = Html::createGridClass(2, 'test');

        // Then
        $this->assertEquals('o-grid-6@test', $result);
    }

    /**
     * @testdox createGridClass returns all HTML tags used in a string.
    */
    public function testGetHtmlTagsReturnsAllHtmlTags()
    {
        // When
        $result = Html::getHtmlTags('<div>div<span>span</span></div>');

        // Then
        $this->assertEquals('<div>', $result[0]);
        $this->assertEquals('<span>', $result[1]);
        $this->assertEquals('</span>', $result[2]);
        $this->assertEquals('</div>', $result[3]);
    }

    /**
     * @testdox createGridClass returns all opening HTML tags used in a string.
    */
    public function testGetHtmlTagsReturnsAllOpeningHtmlTags()
    {
        // When
        $result = Html::getHtmlTags('<div>div<span>span</span></div>', false);

        // Then
        $this->assertEquals('<div>', $result[0]);
        $this->assertEquals('<span>', $result[1]);
        $this->assertArrayNotHasKey("2", $result);
        $this->assertArrayNotHasKey("3", $result);
    }

    /**
     * @testdox createGridClass returns empty array if no tags are present.
    */
    public function testGetHtmlTagsReturnsEmptyArray()
    {
        // When
        $result = Html::getHtmlTags('test');

        // Then
        $this->assertEmpty($result);
    }

    /**
     * @testdox attributesToString returns false if empty or isnt an array
    */
    public function testAttributesToStringReturnsFalseIfFaultyValue()
    {
        // When
        $result = Html::attributesToString(null);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox attributesToString converts attributes array to a string
    */
    public function testAttributesToStringReturnsStringOfAttributes()
    {
        // When
        $result = Html::attributesToString(['a', 'b', 'c']);

        // Then
        $this->assertEquals('0="a" 1="b" 2="c"', $result);
    }

    /**
     * @testdox attributesToString converts and filters attributes array to a string
    */
    public function testAttributesToStringReturnsFilteredAttributes()
    {
        // When
        $result = Html::attributesToString(['a', null, 'c', true]);

        // Then
        $this->assertEquals('0="a" 2="c"', $result);
    }

    /**
     * @testdox attributesToString returns an empty array when string contains no HTML elements with attributes
    */
    public function testGetHtmlAttributesReturnEmptyArrayWhenStringContainsNoElementWithAttributes()
    {
        // When
        $result = Html::getHtmlAttributes('<div>div<span>span</span></div>');

        // Then
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * @testdox attributesToString returns an array of attributes when string contains HTML elements with attributes.
    */
    public function testGetHtmlAttributesReturnArrayContainingElementsWithAttributes()
    {
        // When
        $result = Html::getHtmlAttributes('<div test="test">div<span>span</span></div>');

        // Then
        $this->assertEquals(' test="test"', $result['test']);
    }

    /**
     * @testdox attributesToString returns a string with removed tags
    */
    public function testStripTagsAndAttsRemovesTagsFromString()
    {
        // When
        $result = Html::stripTagsAndAtts('<div test="test">div<span>span</span></div>');

        // Then
        $this->assertEquals('divspan', $result);
    }

    /**
     * @testdox attributesToString returns a string with removed all not allowed tags.
    */
    public function testStripTagsAndAttsReturnsStringWithAllowedTags()
    {
        // When
        $result = Html::stripTagsAndAtts('<div test="test">div<span>span</span></div>', ['div']);

        // Then
        $this->assertEquals('<div>divspan</div>', $result);
    }

    /**
     * @testdox attributesToString returns a string with removed not allowed tags and keeping allowed attributes.
    */
    public function testStripTagsAndAttsReturnsStringWithAllowedAttributes()
    {
        // When
        $result = Html::stripTagsAndAtts('<div test="test">div<span>span</span></div>', ['div'], ['test']);

        // Then
        $this->assertEquals('<div test="test">divspan</div>', $result);
    }
}
