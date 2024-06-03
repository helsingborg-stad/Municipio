<?php

namespace Municipio\SchemaData\Utils;

use AcfService\Contracts\GetField;
use PHPUnit\Framework\TestCase;

class GetSchemaTypeFromPostType extends TestCase
{
    public function testReturnsNullIfEmptyString()
    {
        $acfService                = $this->getAcfService();
        $getSchemaTypeFromPostType = new GetSchemaTypeFromPostType($acfService);
        $this->assertNull($getSchemaTypeFromPostType->getSchemaTypeFromPostType('post_type_without_schema_type'));
    }

    private function getAcfService(): GetField
    {
        return new class implements GetField {
            public function getField(string $selector, int|false|string $postId = false, bool $formatValue = true, bool $escapeHtml = false)
            {
                return [
                    'schema' => [
                        'post_type_without_schema_type' => '',
                        'post_type_with_schema_type'    => 'Airline',
                    ]
                ][$selector][$postId] ?? null;
            }
        };
    }
}
