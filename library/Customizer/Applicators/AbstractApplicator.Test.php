<?php

use Municipio\Customizer\Applicators\AbstractApplicator as AbstractApplicator;
use PHPUnit\Framework\Error\Error;

class AbstractApplicatorTest extends WP_UnitTestCase
{
    protected $sut = null;

    public function set_up()
    {
        $this->sut = new class extends AbstractApplicator
        {
        };
    }

    public function tear_down()
    {
        $this->sut = null;
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testIsFieldTypeReturnsFalseWhenNoOutput()
    {
        // Given
        $field = [];
        $lookForType = "";

        // When
        $result = $this->invokeMethod($this->sut, 'isFieldType', [$field, $lookForType]);

        // Then
        $this->assertEquals($result, false);
    }

    public function testIsFieldTypeReturnsTrueWhenOutputTypeMatchesLookForType()
    {
        // Given
        $field = array('output' => [['type' => 'foo']]);
        $lookForType = 'foo';

        // When
        $result = $this->invokeMethod($this->sut, 'isFieldType', [$field, $lookForType]);

        // Then
        $this->assertTrue($result);
    }

    /**
     * @dataProvider provideValidOperators
     */
    public function testIsValidOperatorReturnsTrueWhenValid($operator)
    {
        // When
        $result = $this->invokeMethod($this->sut, 'isValidOperator', [$operator]);

        // Then
        $this->assertTrue($result);
    }

    public function provideValidOperators()
    {
        return array(['=='], ['==='], ['!='], ['<>'], ['!=='], ['>'], ['<'], ['>='], ['<='], ['<=>']);
    }

    public function testIsValidOperatorReturnsFalseWhenInValid()
    {

        // Given
        $operator = 'foo';

        // When
        $result = $this->invokeMethod($this->sut, 'isValidOperator', [$operator]);

        // Then
        $this->assertFalse($result);
    }

    public function testHasFilterContextsReturnsFalseWhenNoContextMatches()
    {

        // Given
        $filterContexts = [array('operator' => '==', 'context' => 'foo')];
        $contexts = ['bar'];

        // When
        $result = $this->invokeMethod($this->sut, 'hasFilterContexts', [$contexts, $filterContexts]);

        // Then
        $this->assertFalse($result);
    }

    public function testHasFilterContextsReturnsTrueWhenContextMatches()
    {

        // Given
        $filterContexts = [array('operator' => '==', 'context' => 'foo')];
        $contexts = ['foo'];

        // When
        $result = $this->invokeMethod($this->sut, 'hasFilterContexts', [$contexts, $filterContexts]);

        // Then
        $this->assertTrue($result);
    }

    public function testHasFilterContextsTriggersErrorWhenInvalidOperator()
    {
        // Given
        $errorMessage = "Provided operator in context for modifier is not valid.";
        $filterContexts = [array('operator' => 'foo')];
        $contexts = [];

        // When
        try {
            $this->invokeMethod($this->sut, 'hasFilterContexts', [$contexts, $filterContexts]);
        } catch(Throwable $th) {
            // Then
            $this->assertEquals($errorMessage, $th->getMessage());
        }
    }
    
    public function testHasFilterContextsTriggersErrorWhenContextIsNotString()
    {
        // Given
        $errorMessage = "Provided context value in context for modifier is not a string.";
        $filterContexts = [array('operator' => '==', 'context' => 123)];
        $contexts = [];

        // When
        try {
            $this->invokeMethod($this->sut, 'hasFilterContexts', [$contexts, $filterContexts]);
        } catch(Throwable $th) {
            // Then
            $this->assertEquals($errorMessage, $th->getMessage());
        }
    }
}
