<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\ElementAttribute;

/**
 * Class ElementAttributeTest
 * @group wp_mock
 */
class ElementAttributeTest extends TestCase
{
    /**
     * @testdox addClass returns false if parameter is not a string or an array.
    */
    public function testAddClassReturnsFalseIfFaultyParameter()
    {
        // When
        $helper = new ElementAttribute();
        $result = $helper->addClass(null);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox addClass adds an array of classes with one value when a string is provided.
    */
    public function testAddClassAddsClassBasedOnString()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->classes);

        // When
        $helper->addClass('class');

        // Then
        $this->assertEquals('class', $helper->classes[0]);
    }

    /**
     * @testdox addClass adds an array of classes with one value when a string is provided.
    */
    public function testAddClassAddsClassesBasedOnArray()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->classes);

        // When
        $helper->addClass(['class1', 'class2']);

        // Then
        $this->assertEquals('class1', $helper->classes[0]);
        $this->assertEquals('class2', $helper->classes[1]);
    }

    /**
     * @testdox addClass adds an array of classes filtering out empty classes.
    */
    public function testAddClassFiltersAndAddsClassesBasedOnArray()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->classes);

        // When
        $helper->addClass(['', 'class2']);

        // Then
        $this->assertCount(1, $helper->classes);
        $this->assertEquals('class2', $helper->classes[0]);
    }

    /**
     * @testdox removeClass return false if parameter is not a string or an array.
    */
    public function testRemoveClassReturnFalseIfFaultyParameter()
    {
        // Given
        $helper = new ElementAttribute();

        // When
        $helper->removeClass(null);

        // Then
        $this->assertEmpty($helper->classes);
    }

    /**
     * @testdox removeClass removes a class when matching class exists in the classes array.
    */
    public function testRemoveClassRemovesClassWhenMatchingExistingClass()
    {
        // Given
        $helper            = new ElementAttribute();
        $helper->classes[] = 'class1';
        $helper->classes[] = 'class2';
        $this->assertNotEmpty($helper->classes);

        // When
        $helper->removeClass('class1');

        // Then
        $this->assertCount(1, $helper->classes);
        $this->assertEquals('class2', $helper->classes[1]);
    }

    /**
     * @testdox removeClass removes a class when matching class exists in the classes array.
    */
    public function testRemoveClassRemovesClassesWhenMatchingExistingClasses()
    {
        // Given
        $helper            = new ElementAttribute();
        $helper->classes[] = 'class1';
        $helper->classes[] = 'class2';
        $helper->classes[] = 'class3';
        $this->assertNotEmpty($helper->classes);

        // When
        $helper->removeClass(['class1', 'class3']);

        // Then
        $this->assertCount(1, $helper->classes);
        $this->assertEquals('class2', $helper->classes[1]);
    }

    /**
     * @testdox addAttribute Add attributes from array.
    */
    public function testAddAttributeAddsAttributesFromArray()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->attributes);

        // When
        $helper->addAttribute(['key1' => 'val1', 'key2' => 'val2']);

        // Then
        $this->assertEquals('val1', $helper->attributes['key1'][0]);
        $this->assertEquals('val2', $helper->attributes['key2'][0]);
    }

    /**
     * @testdox addAttribute Adds attribute from string
    */
    public function testAddAttributeAddsAttributesFromString()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->attributes);

        // When
        $helper->addAttribute('test', 'val');

        // Then
        $this->assertEquals('val', $helper->attributes['test'][0]);
    }

    /**
     * @testdox addAttribute Adds attribute from string
    */
    public function testAddAttributeAddsAttributesFromStringAndValueArray()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->attributes);

        // When
        $helper->addAttribute('test', ['val1', 'val2']);

        // Then
        $this->assertEquals('val1', $helper->attributes['test'][0]);
        $this->assertEquals('val2', $helper->attributes['test'][1]);
    }

    /**
     * @testdox addAttribute Returns false.
     * @dataProvider addAttributeProvider
    */
    public function testAddAttributeReturnsFalse($attributes, $value)
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->attributes);

        // When
        $result = $helper->addAttribute($attributes, $value);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox newAttribute adds a new attribute from strings.
    */
    public function testNewAttributeAddsNewAttributeFromStrings()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->attributes);

        // When
        $helper->newAttribute('key1', 'val1');

        // Then
        $this->assertEquals('val1', $helper->attributes['key1'][0]);
    }

    /**
     * @testdox newAttribute adds a new attribute with multiple values
    */
    public function testNewAttributeAddsNewAttributeWithMultipleValues()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->attributes);

        // When
        $helper->newAttribute('key1', ['val1', 'val2']);

        // Then
        $this->assertEquals('val1', $helper->attributes['key1'][0]);
        $this->assertEquals('val2', $helper->attributes['key1'][1]);
    }

    /**
     * @testdox newAttribute adds new values to existing key.
    */
    public function testNewAttributeAddsNewValueToExistingAttribute()
    {
        // Given
        $helper                       = new ElementAttribute();
        $helper->attributes['key1'][] = 'test';

        // When
        $helper->newAttribute('key1', ['val1', 'val2']);

        // Then
        $this->assertEquals('test', $helper->attributes['key1'][0]);
        $this->assertEquals('val1', $helper->attributes['key1'][1]);
        $this->assertEquals('val2', $helper->attributes['key1'][2]);
    }

    /**
     * @testdox newAttribute returns false if faulty values
    */
    public function testNewAttributeReturnsFalse()
    {
        // Given
        $helper = new ElementAttribute();

        // When
        $result = $helper->newAttribute(false, null);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getClasses returns classes
    */
    public function testGetClassesReturnsClasses()
    {
        // Given
        $helper            = new ElementAttribute();
        $helper->classes[] = 'class1';
        $helper->classes[] = 'class2';

        // When
        $result = $helper->getClasses();

        // Then
        $this->assertEquals('class1', $result[0]);
        $this->assertEquals('class2', $result[1]);
    }

    /**
     * @testdox getClasses returns null if no classes are found.
    */
    public function testGetClassesReturnsNull()
    {
        // Given
        $helper = new ElementAttribute();

        // When
        $result = $helper->getClasses();

        // Then
        $this->assertNull($result);
    }

    /**
     * @testdox getAttributes returns attributes without classes
    */
    public function testGetAttributesReturnsAttributesWithoutClasses()
    {
        // Given
        $helper                       = new ElementAttribute();
        $helper->attributes['key1'][] = 'val1';
        $helper->attributes['key2'][] = 'val2';

        // When
        $result = $helper->getAttributes(false);

        // Then
        $this->assertEquals('val1', $result['key1'][0]);
        $this->assertEquals('val2', $result['key2'][0]);
        $this->assertCount(2, $result);
    }

    /**
     * @testdox getAttributes returns attributes with classes
    */
    public function testGetAttributesReturnsAttributesWithClasses()
    {
        // Given
        $helper                       = new ElementAttribute();
        $helper->attributes['key1'][] = 'val1';
        $helper->attributes['key2'][] = 'val2';
        $helper->classes[]            = 'val1';
        $helper->classes[]            = 'val2';

        // When
        $result = $helper->getAttributes(true);

        // Then
        $this->assertEquals('val1', $result['key1'][0]);
        $this->assertEquals('val2', $result['key2'][0]);
        $this->assertEquals('val1', $result['class'][0]);
        $this->assertEquals('val2', $result['class'][1]);
        $this->assertCount(3, $result);
    }

    /**
     * @testdox getAttributes returns null if no attributes or classes
    */
    public function testGetAttributesReturnsNull()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->classes);
        $this->assertEmpty($helper->attributes);

        // When
        $result = $helper->getAttributes(true);

        // Then
        $this->assertNull($result);
    }

    /**
     * @testdox outputAttributes returns false if no attributes
    */
    public function testOutputAttributesReturnFalse()
    {
        // Given
        $helper = new ElementAttribute();
        $this->assertEmpty($helper->classes);
        $this->assertEmpty($helper->attributes);

        // When
        $result = $helper->outputAttributes(true);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox outputAttributes returns string with attributes and classes.
    */
    public function testOutputAttributesReturnsStringOfClassesAndAttributes()
    {
        // Given
        $helper                       = new ElementAttribute();
        $helper->attributes['key1'][] = 'val1';
        $helper->attributes['key2'][] = 'val2';
        $helper->classes[]            = 'val1';
        $helper->classes[]            = 'val2';

        // When
        $result = $helper->outputAttributes(true);

        // Then
        $this->assertEquals('class="val1 val2" key1="val1" key2="val2"', $result);
    }

    /**
     * @testdox outputAttributes returns string with attributes
    */
    public function testOutputAttributesReturnsStringOfAttributes()
    {
        // Given
        $helper                       = new ElementAttribute();
        $helper->attributes['key1'][] = 'val1';
        $helper->attributes['key2'][] = 'val2';
        $helper->classes[]            = 'val1';
        $helper->classes[]            = 'val2';

        // When
        $result = $helper->outputAttributes(false);

        // Then
        $this->assertEquals('key1="val1" key2="val2"', $result);
    }

    /**
     * @testdox attributesToString returns string with attributes
    */
    public function testAttributesToStringReturnsAttributeStringFromArray()
    {
        // Given
        $array = [
            'key1'  => [
                'val1'
            ],
            'key2'  => [
                'val2'
            ],
            'class' => [
                'val1',
                'val2'
            ]
        ];

        // When
        $result = ElementAttribute::attributesToString($array);

        // Then
        $this->assertEquals('key1="val1" key2="val2" class="val1 val2"', $result);
    }

    /**
     * @testdox attributesToString returns string with attributes skipping faulty values
    */
    public function testAttributesToStringReturnsAttributeStringFromArrayFilteringFaulty()
    {
        // Given
        $array = [
            ''      => [
                'val1'
            ],
            'key2'  => [
                'val2'
            ],
            'class' => false
        ];

        // When
        $result = ElementAttribute::attributesToString($array);

        // Then
        $this->assertEquals('key2="val2"', $result);
    }

    /**
     * @testdox attributesToString returns string with attributes
    */
    public function testAttributesToStringReturnsFalse()
    {
        // Given
        $array = [];

        // When
        $result = ElementAttribute::attributesToString($array);

        // Then
        $this->assertFalse($result);
    }



    /**
     * Data provider for addAttribute
    */
    public function addAttributeProvider()
    {
        return [
            [null, 'val'],
            [[], 'val'],
            ['', 'val'],
            ['', null],
            ['val', ''],
            ['val', []]
        ];
    }
}
