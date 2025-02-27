<?php

namespace Municipio\ExternalContent\PropertyPathFilter;

use PHPUnit\Framework\TestCase;

class JsonStringPropertyPathFilterTest extends TestCase {
    
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated() {
        $jsonStringPropertyPathFilter = new JsonStringPropertyPathFilter();
        $this->assertInstanceOf(JsonStringPropertyPathFilter::class, $jsonStringPropertyPathFilter);
    }

    /**
     * @testdox filter() throws if input parameter is not a json string
     */
    public function testFilterTakesAJsonStringAsInputParameter() {
        $this->expectException(\InvalidArgumentException::class);
        (new JsonStringPropertyPathFilter())->filter('not a json string');
    }

    /**
     * @testdox filter() does not throw if input parameter is a json string
     */
}