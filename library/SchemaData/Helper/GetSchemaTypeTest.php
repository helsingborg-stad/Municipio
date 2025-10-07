<?php

namespace Municipio\SchemaData\Helper;

use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\AcfService;
use PHPUnit\Framework\TestCase;

class GetSchemaTypeTest extends TestCase
{
    #[TestDox('getSchemaTypesInUse() throws an exception if the AcfService is not set.')]
    public function testGetSchemaTypesInUseThrowsExceptionIfAcfServiceNotSet()
    {
        $this->expectException(\Exception::class);
        GetSchemaType::getSchemaTypesInUse();
    }

    #[TestDox('getSchemaTypesInUse() does not throw if the AcfService is set.')]
    public function testGetSchemaTypesInUseDoesNotThrowIfAcfServiceSet()
    {
        GetSchemaType::setAcfService(new FakeAcfService());
        GetSchemaType::getSchemaTypesInUse();

        $this->assertTrue(true);
    }

    #[TestDox('getPostTypesFromSchemaType() returns an array of post types associated with the given schema type.')]
    public function testGetPostTypesFromSchemaType()
    {
        GetSchemaType::setAcfService(new FakeAcfService(['getField' => function ($key, $postId) {
            return [
                'option' => [
                    'post_type_schema_types' => [
                        ['post_type' => 'post_type_1', 'schema_type' => 'schema_type_1'],
                        ['post_type' => 'post_type_2', 'schema_type' => 'schema_type_1'],
                        ['post_type' => 'post_type_3', 'schema_type' => 'schema_type_2'],
                    ]
                ]

            ][$postId][$key];
        }]));

        $postTypes = GetSchemaType::getPostTypesFromSchemaType('schema_type_1');

        $this->assertEquals(['post_type_1', 'post_type_2'], $postTypes);
    }
}
