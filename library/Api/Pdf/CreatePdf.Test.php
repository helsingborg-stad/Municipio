<?php

namespace Municipio\Api\Pdf\Test;

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

        $pdfHelper = Mockery::mock(PdfHelperInterface::class);
        $pdfHelper->shouldReceive('systemHasSuggestedDependencies')->andReturn(false);
        $pdfHelper->shouldReceive('getThemeMods')->andReturn([]);
        $pdfHelper->shouldReceive('getFonts')->andReturn([]);
        WP_Mock::userFunction('render_blade_view', ['times' => 1, 'return' => $html]);
        $createPdf = new CreatePdf($pdfHelper);

        // When
        $result = $createPdf->getHtmlFromView([$this->mockPost()]);

        // Then
        $this->assertEquals($expectedHtml, $result);
    }
}
