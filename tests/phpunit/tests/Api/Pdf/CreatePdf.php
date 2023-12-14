<?php

namespace Municipio\Tests\Api\Pdf;

use Mockery;
use Municipio\Api\Pdf\CreatePdf;
use Municipio\Api\Pdf\PdfHelperInterface;
use WP_Mock;
use WP_Mock\Tools\TestCase;

class CreatePdfTest extends TestCase
{
    /**
     * testdox renderView() removes unsupported image types.
     */
    public function testRenderViewRemovesUnsupportedImageTypes()
    {
        // Given
        $imageTags = [
            '<img src="https://foo.bar/img.jpg"/>',
            '<img src="https://foo.bar/img.png"/>',
            '<img src="https://foo.bar/img.gif"/>',
            '<img src="https://foo.bar/img.jpeg"/>',
        ];
        $html = join('', $imageTags);
        $expectedHtml = '<img src="https://foo.bar/img.jpg"/><img src="https://foo.bar/img.jpeg"/>';

        $mockExtensionLoaded = \tad\FunctionMocker\FunctionMocker::replace('extension_loaded', false);
        $pdfHelper = Mockery::mock(PdfHelperInterface::class);
        $pdfHelper->shouldReceive('getThemeMods')->andReturn([]);
        $pdfHelper->shouldReceive('getFonts')->andReturn([]);
        WP_Mock::userFunction('render_blade_view', ['times' => 1, 'return' => $html]);
        $createPdf = new CreatePdf($pdfHelper);

        // When
        $result = $createPdf->getHtmlFromView([$this->mockPost()]);

        // Then
        $mockExtensionLoaded->wasCalledOnce();
        $mockExtensionLoaded->wasCalledWithOnce(['gd']);
        $this->assertEquals($expectedHtml, $result);
    }
}
