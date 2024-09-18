<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\GoogleTranslate;
use Mockery;
use WP_Mock;

/**
 * Class GoogleTranslate
 * @group wp_mock
 */
class GoogleTranslateTest extends TestCase
{
    /**
     * @testdox GoogleTranslate Construct sets wanted filters.
    */
    public function testGoogleTranslateConstructSetupFilters()
    {
        // Given
        WP_Mock::userFunction('get_field', [
            'return' => 'word1, word2, word3'
        ]);

        WP_Mock::expectFilterAdded('the_content', WP_Mock\Functions::type('callable'));
        WP_Mock::expectFilterAdded('the_excerpt', WP_Mock\Functions::type('callable'));
        WP_Mock::expectFilterAdded('the_title', WP_Mock\Functions::type('callable'));

        // When
        new GoogleTranslate();

        // Then
        WP_Mock::assertFiltersCalled();
    }

    /**
     * @testdox shouldReplaceWords replaces words not supposed to be translated.
    */
    public function testShouldReplaceWordsReplacesWordsThatShouldNotBeTranslated()
    {
        // Given
        $instance = Mockery::mock(GoogleTranslate::class)->makePartial();
        $result   = $instance->shouldReplaceWords('<div>test content</div> content Test', 'test, Test');

        $this->assertEquals(
            '<div> <span translate="no">test</span> content</div> content  <span translate="no">Test</span>',
            $result
        );
    }

     /**
     * @testdox shouldReplaceWords does nothing if no words to replace are found.
    */
    public function testShouldReplaceReturnsContentNoWordsToReplaceFound()
    {
        // Given
        $instance = Mockery::mock(GoogleTranslate::class)->makePartial();
        $result   = $instance->shouldReplaceWords('Tortor Parturient Elit Nibh Fringilla', 'soup');

        $this->assertEquals(
            'Tortor Parturient Elit Nibh Fringilla',
            $result
        );
    }
}
