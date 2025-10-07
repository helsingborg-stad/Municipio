<?php

namespace Municipio\Helper;

use PHPUnit\Framework\TestCase;
use Municipio\Helper\FormatObject;

/**
 * Class FormatObjectTest
 */
class FormatObjectTest extends TestCase
{
    /**
     * @testdox camelCase returns camelCased Object when array or object received.
     * @dataProvider camelCaseArrayObjectProvider
    */
    public function testCamelCaseReturnsCamelCasedObjectWhenArrayOrObjectReceived($item)
    {
        // When
        $result = FormatObject::camelCase($item);

        // Then
        $this->assertEquals((object) ['key1' => 'value_1', 'keyTwo' => 'value_2'], $result);
    }

    /**
     * @testdox camelCase throw an exception when not receiving an array, object, or string.
     * @expectException Exception
    */
    public function testCamelCaseTrowAnExceptionWhenFaultyValue()
    {
        // When
        $this->expectException(\Exception::class);
        FormatObject::camelCase(null);
    }

    /**
     * @testdox camelCase returns camelCased string when string received.
     * @dataProvider camelCaseStringProvider
    */
    public function testCamelCaseReturnsCamelCasedStringWhenStringReceived($string)
    {
        // When
        $result = FormatObject::camelCase($string);

        // Then
        $this->assertEquals('testString', $result);
    }


    /**
     * @testdox mapArrayKeys returns filtered array keys. Also skips certain keys.
    */
    public function testMapArrayKeysReturnsFilteredArrayKeys()
    {
        // When
        $result = FormatObject::mapArrayKeys(function ($key) {
            return intval($key) ? $key . "test" : $key;
        }, $this->mapArrayKeysArray());

        // Then
        $this->assertArrayHasKey('1test', $result);
        $this->assertArrayHasKey('2test', $result);
        $this->assertArrayHasKey('abc', $result);
        $this->assertArrayHasKey('1test', $result['testList']);
        $this->assertArrayHasKey('1', $result['classList']);
        $this->assertArrayNotHasKey('1test', $result['classList']);
        $this->assertArrayHasKey('1', $result['attributeList']);
        $this->assertArrayNotHasKey('1test', $result['attributeList']);
    }

    /**
     * @testdox mapArrayKeys returns filtered array keys. Also skips certain keys.
    */
    public function testCreateNodeFromString()
    {
        // Given
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . '<a href="#test"></a>');

        // When
        $result = FormatObject::createNodeFromString($dom, '<a href="#test"></a>');

        // Then
        $anchorTags = $result->getElementsByTagName('a');

        $this->assertInstanceOf(\DOMNode::class, $result);
        $this->assertEquals('html', $result->tagName);
        $this->assertEquals(1, $anchorTags->length);
    }


    /**
     * Mocked array
    */
    private function mapArrayKeysArray()
    {
        return
            [
                '1'             => '',
                '2'             => '',
                'abc'           => '',
                'classList'     => [
                    '1' => ''
                ],
                'attributeList' => [
                    '1' => ''
                ],
                'testList'      => [
                    '1' => ''
                ]
            ];
    }


    /**
     * Provider for camelCase
    */
    public static function camelCaseArrayObjectProvider()
    {
        return [
            [(object) ['key_1' => 'value_1', 'key_two' => 'value_2']],
            [(array) ['key_1' => 'value_1', 'key_two' => 'value_2']],
            [(array) ['key-1' => 'value_1', 'key-two' => 'value_2']],
            [(object) ['key-1' => 'value_1', 'key-two' => 'value_2']]
        ];
    }

    /**
     * Provider for camelCase
    */
    public static function camelCaseStringProvider()
    {
        return [
            ['test_string'],
            ['test__string'],
            ['testString'],
            ['test_string_'],
            ['_test_string'],
            ['test-string'],
            ['test--string'],
            ['test-string-'],
            ['-test-string']
        ];
    }
}
