<?php

use Municipio\Customizer\Applicators\ComponentData;

class ComponentDataTest extends WP_UnitTestCase
{
    public function testGetAllFieldsReturnsArray()
    {
        // Given
        $sut = new ComponentData();

        // When
        $fields = $sut->getAllFields();

        // Then
        $this->assertIsArray($fields);
    }

    public function testGetAllFieldsReturnsNonEmptyArray()
    {
        // Given
        $sut = new ComponentData();

        // When
        $fields = $sut->getAllFields();

        // Then
        $this->assertNotEmpty($fields);
    }

    public function testBuildFilterDataReturnsMultidimensionalArray()
    {
        // Given
        $sut = new ComponentData();
        $dataKey = 'parentKey.childKey';
        $value = 'dataValue';
        $expectedValue = ['parentKey' => ['childKey' => 'dataValue']];

        // When
        $filterData = $sut->buildFilterData($dataKey, $value);

        // Then
        $this->assertEqualsCanonicalizing($expectedValue, $filterData);
    }
}
