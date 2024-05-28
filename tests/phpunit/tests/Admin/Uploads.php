<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Admin\Uploads;
use WP_Mock;
use Mockery;
use tad\FunctionMocker\FunctionMocker;

/**
 * Class TermTest
 * @group wp_mock
 */
class UploadsTest extends TestCase
{
    /**
     * @testdox getTermColour returns false if no taxonomy and no term object.
    */
    public function testActionsCalled()
    {
        // When
        $instance = new Uploads();
        WP_Mock::expectActionAdded('add_attachment', [$instance, 'convertWOFFToTTF']);
        $instance->addHooks();

        // Then
        WP_Mock::assertActionsCalled();
    }

    /**
     * @testdox ConvertWOFFToTTF returns false if mime type does not match WOFF.
     * @dataProvider dataProviderConvertWOFFToTTF
    */
    public function testConvertWOFFToTTFReturnsFalse($getPostMimeTypeResponse)
    {
        // Given
        $this->mockedUserFunctions($getPostMimeTypeResponse);
        // When
        $instance = new Uploads();
        $result   = $instance->convertWOFFToTTF(1);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox ConvertWOFFToTTF returns convert helper if mime type matches WOFF.
     * @runInSeparateProcess
    */
    public function testConvertWOFFToTTFReturnsConvert()
    {
        // Given
        $this->mockedUserFunctions('application/font-woff');
        Mockery::mock('alias:' . \Municipio\Helper\FileConverters\WoffConverter::class)
        ->shouldReceive('convert')
        ->once()
        ->andReturn(true);

        // When
        $instance = new Uploads();
        $result   = $instance->convertWOFFToTTF(1);

        // Then
        $this->assertTrue($result);
    }

    /**
     * Mocked user functions
    */
    private function mockedUserFunctions($getPostMimeType = false, $getAttachedFile = false)
    {
        WP_Mock::userFunction('get_post_mime_type', [
            'return' => $getPostMimeType
        ]);

        WP_Mock::userFunction('get_attached_file', [
            'return' => $getAttachedFile
        ]);

        WP_Mock::userFunction('add_post_meta', [
            'return' => true
        ]);
    }

    /**
     * Data provider for testConvertWOFFToTTF
    */
    public function dataProviderConvertWOFFToTTF()
    {
        return [
            [false],
            ['font/ttf']
        ];
    }
}
