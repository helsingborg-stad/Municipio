<?php

namespace Municipio\Api\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Municipio\Helper\Image;
use Municipio\Api\Pdf\PdfHelper;

class CreatePdf
{
    /**
     * Renders a PDF view for the specified posts and cover information.
     *
     * @param array|false $posts     Array of posts or false if not available.
     * @param array|false $cover     Cover information or false if not available.
     * @param string      $fileName  Name of the PDF file.
     */
    public function renderView($posts = false, $cover = false, string $fileName = 'print') {
        $pdfHelper = new PdfHelper();
        $styles = $pdfHelper->getThemeMods();
        $fonts = $pdfHelper->getFonts($styles);
        $lang = $this->getLang();

        if (!empty($posts)) {
            $html = render_blade_view('partials.content.pdf.layout', [
                'posts'     => $posts,
                'styles'    => $styles,
                'cover'     => $cover,
                'fonts'     => $fonts,
                'lang'      => $lang
            ]);

            $html = $this->replaceHtmlFromRegex(
                ['/<script(?!.*?class="pdf-script")[^>]*>.*?<\/script>/s'],
                $html
            );

            $this->renderPdf($html, $fileName);
        }
    }

    /**
     * Retrieves language-related information.
     *
     * @return array Language-related information.
     */
    private function getLang() {
        $lang = [
            'generatedPdf' => __('Generated PDF', 'municipio')
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
    private function replaceHtmlFromRegex(array $patterns, string $html = '') {
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
    private function renderPdf(string $html, string $fileName) {
        $dompdf = new Dompdf([
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $dompdf->stream($fileName, ['Attachment' => 0]);
    }
}

