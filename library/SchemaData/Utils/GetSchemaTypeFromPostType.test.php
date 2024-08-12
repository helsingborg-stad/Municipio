<?php

namespace Municipio\SchemaData\Utils;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;

class GetSchemaTypeFromPostTypeTest extends TestCase
{
    public function testReturnsNullIfEmptyString()
    {
        $returnCallback            = fn($selector, $postId) => $postId  === 'post_type_without_schema_type_options' && $selector === 'schema' ? '' : null;
        $acfService                = new FakeAcfService(['getField' => $returnCallback]);
        $getSchemaTypeFromPostType = new GetSchemaTypeFromPostType($acfService);
        $this->assertNull($getSchemaTypeFromPostType->getSchemaTypeFromPostType('post_type_without_schema_type'));
    }
}
