<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

use PHPUnit\Framework\TestCase;

class TypesenseJsonConverterTest extends TestCase {
    /**
     * @testdox converts Typesense formatted api response to schema objects
     */
    public function testSuccess() {
        $json = '{
            "hits":[
                {
                    "document": {
                        "@type":"Thing"
                    }
                }
            ]    
        }';

        $converter = new TypesenseJsonConverter();
        $result = $converter->transform($json);

        $this->assertEquals('Thing', $result[0]->getProperty('@type'));
    }

    /**
     * @testdox skips hits without document
     */
    public function testSkipsHitsWithoutDocument() {
        $json = '{
            "hits":[
                {
                    "foo": "bar"
                },
                {
                    "document": {
                        "@type":"Thing"
                    }
                }
            ]    
        }';

        $converter = new TypesenseJsonConverter();
        $result = $converter->transform($json);

        $this->assertCount(1, $result);
        $this->assertEquals('Thing', $result[0]->getProperty('@type'));
    }
    
    /**
     * @testdox returns empty array when json empty
     */
    public function testEmptyJson() {
        $json = '';

        $converter = new TypesenseJsonConverter();
        $result = $converter->transform($json);

        $this->assertEquals([], $result);
    }
}