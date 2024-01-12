<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\Term;
use WP_Mock;
use Mockery;
use tad\FunctionMocker\FunctionMocker;

/**
 * Class TermTest
 */
class TermTest extends TestCase
{
    /**
     * @testdox getTermColour returns false if no taxonomy and no term object.
    */
    public function testGetTermColourReturnsFalseIfNoTermOrTaxonomy()
    {
        // When
        $result = Term::getTermColour(null);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getTermColour returns false if no term but taxonomy.
    */
    public function testGetTermColourReturnsFalseIfNoTermButTaxonomy()
    {
        // Given

        // When
        $result = Term::getTermColour(null, 'test');

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getTermColour returns color if term object has a color set.
    */
    public function testGetTermColourReturnsColorIfReceivedTermObject()
    {
        // Given
        $this->mockUserFunctions();

        // When
        $result = Term::getTermColour($this->mockTermObject());

        // Then
        $this->assertEquals('#000000', $result);
    }

    /**
     * @testdox getTermColour returns adds # to the color if missing.
    */
    public function testGetTermColourAddsPoundToColorIfMissing()
    {
        // Given
        $this->mockUserFunctions();

        // When
        $result = Term::getTermColour($this->mockTermObject('000000'));

        // Then
        $this->assertEquals('#000000', $result);
    }

    /**
     * @testdox getTermColour returns color if term id is provided.
    */
    public function testGetTermColourReturnsColorIfTermIdProvided()
    {
        // Given
        $this->mockUserFunctions();

        // When
        $result = Term::getTermColour(1, 'test');

        // Then
        $this->assertEquals('#000000', $result);
    }

    /**
     * @testdox getTermColour returns color if term slug is provided.
    */
    public function testGetTermColourReturnColorIfTermSlugProvided()
    {
        // Given
        $this->mockUserFunctions();

        // When
        $result = Term::getTermColour('test', 'test');

        // Then
        $this->assertEquals('#000000', $result);
    }

    /**
     * @testdox getTermColour returns false if no term found.
    */
    public function testGetTermColourReturnsFalseIfNoTerm()
    {
        // Given
        WP_Mock::userFunction('get_field', [
            'times'  => 0,
            'return' => false
        ]);

        WP_Mock::userFunction('get_term_by', [
            'return' => false
        ]);

        // When
        $result = Term::getTermColour(1, 'test');

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getTermColour returns false no ancestors.
    */
    public function testGetTermColourReturnsFalseIfNoAncestors()
    {
        // Given
        $this->mockUserFunctions(false, "", []);

        // When
        $result = Term::getTermColour($this->mockTermObject(), 'test');

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getTermColour returns false if ancestor does not have term color set.
    */
    public function testGetTermColourReturnsFalseIfAncestorsDoesNotHaveTermColor()
    {
        // Given
        $this->mockUserFunctions(false);

        // When
        $result = Term::getTermColour($this->mockTermObject(), 'test');

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getTermColour returns false if ancestor does not have term color set.
    */
    public function testGetTermColour()
    {
        // Given
        $this->mockUserFunctions(false);

        // When
        $result = Term::getTermColour($this->mockTermObject(), 'test');

        // Then
        $this->assertFalse($result);
    }

    /* TESTA ANCESTOR DELEN */

    /**
     * Mock term object
    */
    private function mockTermObject()
    {
        $termMock = Mockery::mock('WP_Term');

        return $termMock;
    }

    /**
     * Mock user functions
    */
    private function mockUserFunctions($getField = '#000000', $getTermBy = "", $getAncestors = [1, 2])
    {
        WP_Mock::userFunction('get_field', [
            'return' => $getField
        ]);

        WP_Mock::userFunction('get_term_by', [
            'return' => $getTermBy !== "" ? $getTermBy : $this->mockTermObject()
        ]);

        WP_Mock::userFunction('get_ancestors', [
            'return' => $getAncestors
        ]);
    }
}
