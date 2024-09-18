<?php

namespace Municipio\Tests\Api\Pdf;

use Mockery;
use Municipio\Api\Pdf\CreatePdf;
use Municipio\Api\Pdf\PdfHelperInterface;
use WP_Mock;
use WP_Mock\Tools\TestCase;
use Municipio\Helper\FileConverters\FileConverterInterface;
use phpmock\mockery\PHPMockery;

/**
 * Class CreatePdfTest
 * @group wp_mock
 */
class CreatePdfTest extends TestCase
{
    /**
     * testdox getHtmlFromView() removes unsupported image types.
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetHtmlFromViewRemovesUnsupportedImageTypes()
    {
        // Given
        $imageTags    = [
            '<img src="https://foo.bar/img.jpg"/>',
            '<img src="https://foo.bar/img.png"/>',
            '<img src="https://foo.bar/img.gif"/>',
            '<img src="https://foo.bar/img.jpeg"/>',
        ];
        $html         = join('', $imageTags);
        $expectedHtml = '<img src="https://foo.bar/img.jpg"/><img src="https://foo.bar/img.jpeg"/>';

        PHPMockery::mock('Municipio\Api\Pdf', 'extension_loaded')->with('gd')->andReturn(false);
        $pdfHelper         = Mockery::mock(PdfHelperInterface::class);
        $woffConverterMock = Mockery::mock('alias:' . FileConverterInterface::class);
        $woffConverterMock->shouldReceive('convert')->andReturn('');
        $pdfHelper->shouldReceive('getThemeMods')->andReturn([]);
        $pdfHelper->shouldReceive('getFonts')->andReturn([]);
        WP_Mock::userFunction('render_blade_view', ['times' => 1, 'return' => $html]);
        $createPdf = new CreatePdf($pdfHelper, $woffConverterMock);

        // When
        $result = $createPdf->getHtmlFromView(['key' => [$this->mockPost()]]);

        // Then
        $this->assertEquals($expectedHtml, $result);
    }

    /**
     * testdox getHtmlFromView() removes scripts without the pdf-script class.
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetHtmlFromViewRemovesScriptTagsWithoutThePdfScriptClass()
    {
        // Given
        $scriptTags   = [
            '<script type="text/javascript" class="pdf-script">console.log("some test code");</script>',
            '<script type="text/javascript" class="foo-bar">console.log("some test code");</script>'
        ];
        $html         = join('', $scriptTags);
        $expectedHtml = '<script type="text/javascript" class="pdf-script">console.log("some test code");</script>';

        PHPMockery::mock('Municipio\Api\Pdf', 'extension_loaded')->with('gd')->andReturn(false);
        $pdfHelper         = Mockery::mock(PdfHelperInterface::class);
        $woffConverterMock = Mockery::mock('alias:' . FileConverterInterface::class);
        $woffConverterMock->shouldReceive('convert')->andReturn('');
        $pdfHelper->shouldReceive('getThemeMods')->andReturn([]);
        $pdfHelper->shouldReceive('getFonts')->andReturn([]);
        WP_Mock::userFunction('render_blade_view', ['times' => 1, 'return' => $html]);
        $createPdf = new CreatePdf($pdfHelper, $woffConverterMock);

        // When
        $result = $createPdf->getHtmlFromView(['key' => [$this->mockPost()]]);

        // Then
        $this->assertEquals($expectedHtml, $result);
    }

     /**
     * testdox getHtmlFromView() Returns empty string if no posts.
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetHtmlFromViewReturnsString()
    {
        // Given
        $html = [
            '<script type="text/javascript" class="pdf-script">console.log("some test code");</script>'
        ];

        $pdfHelper         = Mockery::mock(PdfHelperInterface::class);
        $woffConverterMock = Mockery::mock('alias:' . FileConverterInterface::class);
        $woffConverterMock->shouldReceive('convert')->andReturn('');
        $pdfHelper->shouldReceive('getThemeMods')->andReturn([]);
        $pdfHelper->shouldReceive('getFonts')->andReturn([]);
        $createPdf = new CreatePdf($pdfHelper, $woffConverterMock);

        // When
        $result = $createPdf->getHtmlFromView([]);

        // Then
        $this->assertIsString($result);
        $this->assertEmpty($result);
    }
}
