<?php

namespace Municipio\Api\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Municipio\Helper\Image;
use Municipio\Api\Pdf\PdfHelper as PDFHelper;
use Municipio\Helper\FileConverters\FileConverterInterface;

/**
 * Class CreatePdf
*/
class CreatePdf
{
    private PdfHelperInterface $pdfHelper;
    private FileConverterInterface $woffHelper;

    /**
     * CreatePdf construct
     *
     * @param PdfHelperInterface $pdfHelper PdfHelper instance.
     * @param FileConverterInterface $woffHelper FileConverter instance.
    */
    public function __construct(PdfHelperInterface $pdfHelper, FileConverterInterface $woffHelper)
    {
        $this->pdfHelper  = $pdfHelper;
        $this->woffHelper = $woffHelper;
    }

    /**
     * Renders a PDF view for the specified posts and cover information.
     *
     * @param array|false $sortedPostsArray     Array of posts or false if not available.
     * @param array|false $cover     Cover information or false if not available.
     * @param string      $fileName  Name of the PDF file.
     */
    public function getHtmlFromView($sortedPostsArray = false, $cover = false): string
    {
        $styles = $this->pdfHelper->getThemeMods();
        $fonts  = $this->pdfHelper->getFonts($styles, $this->woffHelper);
        $lang   = $this->getLang();
        if (!empty($sortedPostsArray) && is_array($sortedPostsArray) && reset($sortedPostsArray)) {
            $html = render_blade_view('partials.content.pdf.layout', [
                'sortedPostsArray'   => $sortedPostsArray,
                'styles'             => $styles,
                'cover'              => $cover,
                'fonts'              => $fonts,
                'lang'               => $lang,
                'hasMoreThanOnePost' => count($sortedPostsArray) > 1 || count(reset($sortedPostsArray)) > 1
            ]);

            $html = $this->replaceHtmlFromRegex([ '/<script(?!.*?class="pdf-script")[^>]*>.*?<\/script>/s', ], $html);

            if (!extension_loaded('gd')) {
                $html = $this->replaceHtmlFromRegex([ '/<img(?![^>]*?\.jp(e)?g)[^>]*>/s', ], $html);
            }

            return $html;
        }

        return "";
    }

    /**
     * Retrieves language-related information.
     *
     * @return array Language-related information.
     */
    private function getLang(): array
    {
        $lang = [
            'generatedPdf'    => __('Generated PDF', 'municipio'),
            'tableOfContents' => __('Table of Contents', 'municipio'),
        ];
        return $lang;
    }

    /**
     * Replaces HTML content based on regular expressions.
     *
     * @param array  $patterns Array of regular expression patterns.
     * @param string $html     HTML content.
     *
     * @return string Modified HTML content.
     */
    private function replaceHtmlFromRegex(array $patterns, string $html = '')
    {
        if (isset($patterns) && is_array($patterns)) {
            foreach ($patterns as $pattern) {
                $html = preg_replace($pattern, '', $html);
            }
        }

        return $html;
    }

    /**
     * Renders a PDF from the provided HTML content.
     *
     * @param string $html     HTML content.
     * @param string $fileName Name of the PDF file.
     */
    public function renderPdf(string $html, string $fileName = 'print')
    {
        $dompdf = new Dompdf([
            'isRemoteEnabled'      => true,
            'isPhpEnabled'         => true,
            'isHtml5ParserEnabled' => true,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($fileName, ['Attachment' => 0]);
    }
}
