<?php

namespace Municipio\Api\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Municipio\Helper\Image;
use Municipio\Api\Pdf\PdfHelper;

class CreatePdf
{
    public function renderView($posts = false, $cover = false) {
        $pdfHelper = new PdfHelper();
        $styles = $pdfHelper->getThemeMods();
        $fonts = $pdfHelper->getFonts($styles);

        if (!empty($posts)) {
            $html = render_blade_view('partials.content.pdf.layout', [
                'posts'     => $posts,
                'styles'    => $styles,
                'cover'     => $cover,
                'fonts'     => $fonts
            ]);

            $html = $this->replaceHtmlFromRegex(
                ['/<script(?!.*?class="pdf-script")[^>]*>.*?<\/script>/s'],
                $html
            );

            $this->renderPdf($html);
        }
    }

    private function replaceHtmlFromRegex(array $patterns, string $html = '') {
        if (isset($patterns) && is_array($patterns)) {
            foreach ($patterns as $pattern) {
                $html = preg_replace($pattern, '', $html);
            }
        }

        return $html;
    }

    private function renderPdf(string $html) {
        $dompdf = new Dompdf([
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $dompdf->stream('dom', ['Attachment' => 0]);
    }
}

