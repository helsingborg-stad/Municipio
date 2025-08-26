<?php

namespace Municipio\SchemaData\ExternalContent\Filter\GetValueByPathFromArray;

use PHPUnit\Framework\TestCase;

class GetValueByPathFromArrayTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $getValueByPath = new GetValueByPathFromArray();
        $this->assertInstanceOf(GetValueByPathFromArray::class, $getValueByPath);
    }

    /**
     * @testdox returns value from array by path
     */
    public function testGetValueByPathReturnsValueFromArrayByPath()
    {
        $array = [ 'foo' => [ 'bar' => 'baz' ] ];

        $getValueByPath = new GetValueByPathFromArray();
        $this->assertEquals('baz', $getValueByPath->getValueByPath($array, 'foo.bar'));
    }

    /**
     * @testdox returns value from object by path when object is transformed to array
     */
    public function testGetValueByPathReturnsValueFromObjectByPath()
    {
        $object = (object) [ 'foo' => (object) [ 'bar' => 'baz' ] ];
        $array  = json_decode(json_encode($object), true);

        $getValueByPath = new GetValueByPathFromArray();
        $this->assertEquals('baz', $getValueByPath->getValueByPath($array, 'foo.bar'));
    }

    /**
     * @testdox if the nested value is an array, return an array containing all the values
     */
    public function testIfTheNestedValueIsAnArrayReturnAnArrayContainingAllTheValues()
    {
        $array = [
            'foo' => [
                ['bar' => 'baz'],
                ['bar' => 'qux']
            ]
        ];

        $getValueByPath = new GetValueByPathFromArray();

        $this->assertEquals(['baz', 'qux'], $getValueByPath->getValueByPath($array, 'foo.bar'));
    }
}
