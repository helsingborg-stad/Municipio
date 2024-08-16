<?php

namespace Municipio\SchemaData;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class LimitSchemaTypesAndPropertiesTest extends TestCase
{
    /**
     * @testdox addHooks() uses valid callbacks
     */
    public function testHooksAreValid()
    {
        $sut = new LimitSchemaTypesAndProperties([], $wpService = new FakeWpService());
        $sut->addHooks();

        $this->assertTrue(method_exists($sut, $wpService->methodCalls['addFilter'][0][1][1]));
        $this->assertTrue(method_exists($sut, $wpService->methodCalls['addFilter'][1][1][1]));
    }

    /**
     * @testdox filterSchemaTypes() returns only allowed schema types
     */
    public function testReturnsOnlyAllowedSchemaTypes()
    {
        $sut = new LimitSchemaTypesAndProperties(['type1' => []], new FakeWpService());
        $this->assertEquals(['type1'], $sut->filterSchemaTypes(['type1', 'type2']));
    }

    /**
     * @testdox filterSchemaProperties() returns only allowed schema properties
     */
    public function testReturnsOnlyAllowedSchemaProperties()
    {
        $sut = new LimitSchemaTypesAndProperties(['type1' => ['prop1']], new FakeWpService());
        $this->assertEquals(['prop1' => 'value1'], $sut->filterSchemaProperties(['prop1' => 'value1', 'prop2' => 'value2'], 'type1'));
    }

    /**
     * @testdox filterSchemaProperties() returns all properties if schema type is not found
     */
    public function testReturnsAllPropertiesIfSchemaTypeIsNotFound()
    {
        $sut = new LimitSchemaTypesAndProperties([], new FakeWpService());
        $this->assertEquals(['someProp'], $sut->filterSchemaProperties(['someProp'], 'SomeSchemaType'));
    }
}
