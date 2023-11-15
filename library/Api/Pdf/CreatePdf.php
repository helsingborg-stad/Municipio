<?php

namespace Municipio\Api\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class CreatePdf
{
    public function __construct()
    {

    }

    public function renderView($ids) {
        $posts = [];
        $postTypes = [];
        if (!empty($ids) && is_array($ids)) {
            foreach ($ids as $id) {
                $post = get_post($id);
                if (!empty($post->post_status) && $post->post_status == 'publish') {
                    $post = \Municipio\Helper\Post::preparePostObjectArchive($post);
                    if (!empty($post->postType)) {
                        array_push($postTypes, $post->postType);
                    }
                    array_push($posts, $post);
                }
            }
        }
        
        $cover = $this->getCover($postTypes);
        $styles = $this->getThemeMods();
        $fonts = $this->getFonts($styles);

        if (!empty($posts)) {
            $html = render_blade_view('partials.content.pdf.layout', [
                'posts' => $posts,
                'styles' => $styles,
                // 'fonts' => $fonts
            ]);

            $this->renderPdf($html);
        }
    }

    private function getCover($postTypes) {
        if (!empty($postTypes) && count($postTypes) === 1) {
            echo '<pre>' . print_r( $postTypes, true ) . '</pre>';
            die;
        } else {

        }
    }

    private function getFonts($styles) {
        // $args = array(
        //     'post_type'      => 'attachment',
        //     'posts_per_page' => -1,
        //     'post_status'    => 'inherit',
        //     'post_mime_type' => 'application/font-woff'
        // );

        // $customFonts = new \WP_Query($args);

        // $heading = $styles['typography_heading'];
        // $base = $styles['typography_base'];
    
        // if (!empty($customFonts->posts) && (!empty($heading['font-family']) || $base['font-family']) && is_array($customFonts->posts)) {
        //     foreach ($customFonts->posts as $font) {
        //         if (!empty($font->post_name)) {
        //             if ($font->post_name == $heading['font-family']) {
        //                 $heading['src'] = !empty($font->guid) ? $font->guid : '';
        //             }
                    
        //             if ($font->post_name == $base['font-family']) {
        //                 $base['src'] = !empty($font->guid) ? $font->guid : '';
        //             }
        //         }
        //     }
        // }

        // return [
        //     'base' => $base,
        //     'heading' => $heading
        // ];
    }

    private function getThemeMods() {
        return get_theme_mods();
    }

    private function renderPdf($html) {
        $dompdf = new Dompdf([
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
 
        $dompdf->render();
        $dompdf->stream('document.pdf', ['Attachment' => 0]);
    }
}

