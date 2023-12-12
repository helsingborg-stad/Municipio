<?php

namespace Municipio\Helper\Test;

use Municipio\Helper\Data;
use PHPUnit\Framework\TestCase;

class DataIsJsonTest extends TestCase {

    /**
     * @testdox isJson returns true when valid json
     * @covers \Municipio\Helper\Data::isJson
     */
    public function testIsJsonReturnsTrueWhenValidJson() {
        // Given
        $json = '{"foo": "bar"}';

        // When
        $result = Data::isJson($json);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @testdox isJson returns false when invalid json
     * @dataProvider invalidJsonDataProvider
     * @covers \Municipio\Helper\Data::isJson
     */
    public function testIsJsonReturnsFalseWhenInvalidJson($json) {
        // When
        $result = Data::isJson($json);

        // Then
        $this->assertFalse($result);
    }

    public function invalidJsonDataProvider() {
        return [ ['{"foo": "bar"'], [null], [false], [array()], ['foo'], ];
    }

}