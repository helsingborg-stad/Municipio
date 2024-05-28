<?php

declare(strict_types=1);

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\Term;
use WP_Mock;
use Mockery;
use tad\FunctionMocker\FunctionMocker;

/**
 * Class TermTest
 * @group wp_mock
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
     * @testdox getTermColour returns ancestor term color.
    */
    public function testGetTermColourReturnsAncestorTermColor()
    {
        // Given
        WP_Mock::userFunction('get_field', [
            'times'           => 2,
            'return_in_order' => [false, '#000000']
        ]);

        WP_Mock::userFunction('get_ancestors', [
            'return' => [1, 2]
        ]);


        // When
        $result = Term::getTermColour($this->mockTermObject(), 'test');

        // Then
        $this->assertEquals('#000000', $result);
    }

    /**
     * @testdox getTermColor returns getTermColour
    */
    public function testGetTermColor()
    {
        $this->mockUserFunctions();

        $result = Term::getTermColor($this->mockTermObject(), 'test');

        $this->assertEquals('#000000', $result);
    }

    /**
     * @testdox getTermIcon returns false if no taxonomy and no WP_Term
    */
    public function testGetTermIconReturnsFalseIfNoTaxonomy()
    {
        $this->mockUserFunctions();

        $result = Term::getTermIcon(1, '');

        $this->assertEquals(false, $result);
    }

    /**
     * @testdox getTermIcon returns false if no term icon type key.
    */
    public function testGetTermIconReturnsFalseNoIconTypeKey()
    {
        WP_Mock::userFunction('get_term_by', [
            'times'  => 1,
            'return' => $this->mockTermObject()
        ]);

        WP_Mock::userFunction('get_field', [
            'times'  => 1,
            'return' => false
        ]);

        $result = Term::getTermIcon(1, 'test');

        $this->assertEquals(false, $result);
    }

    /**
     * @testdox getTermIcon returns false if no term icon type key.
    */
    public function testGetTermIconReturnsFalseIfNoTerm()
    {
        WP_Mock::userFunction('get_term_by', [
            'times'  => 1,
            'return' => false
        ]);

        WP_Mock::userFunction('get_field', [
            'times'  => 0,
            'return' => false
        ]);

        $result = Term::getTermIcon(1, 'test');

        $this->assertEquals(false, $result);
    }

    /**
     * @testdox getTermIcon returns array if type equals "svg".
    */
    public function testGetTermIconReturnsArrayWhenTypeEqualsSvg()
    {
        $this->mockUserFunctions([
            'type' => 'svg',
            'svg'  => [
                'ID'          => 1,
                'description' => 'test'
            ]
        ]);

        $result = Term::getTermIcon(1, 'test');

        $this->assertArrayHasKey('src', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('alt', $result);
    }

    /**
     * @testdox getTermIcon returns array if type equals "icon".
    */
    public function testGetTermIcon()
    {
        $this->mockUserFunctions([
            'type'          => 'icon',
            'material_icon' => 'test'
        ]);

        $result = Term::getTermIcon(1, 'test');

        $this->assertArrayHasKey('src', $result);
        $this->assertArrayHasKey('type', $result);
    }

    /**
     * Mock term object
    */
    private function mockTermObject()
    {
        $termMock           = Mockery::mock('WP_Term');
        $termMock->term_id  = 1;
        $termMock->taxonomy = 'test-taxonomy';

        return $termMock;
    }

    /**
     * Mock user functions
    */
    private function mockUserFunctions(
        $getField = '#000000',
        $getTermBy = "",
        $getAncestors = [1, 2],
        $wpGetAttachmentImageUrl = 'https://test.test'
    ) {
        WP_Mock::userFunction('get_field', [
            'return' => $getField
        ]);

        WP_Mock::userFunction('get_term_by', [
            'return' => $getTermBy !== "" ? $getTermBy : $this->mockTermObject()
        ]);

        WP_Mock::userFunction('get_ancestors', [
            'return' => $getAncestors
        ]);

        WP_Mock::userFunction('wp_get_attachment_image_url', [
            'return' => $wpGetAttachmentImageUrl
        ]);
    }
}
