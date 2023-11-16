<?php

namespace Municipio\Api\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use Municipio\Helper\Image;

class CreatePdf
{
    private $defaultPrefix = 'default';

    public function __construct()
    {

    }

    public function renderView($ids) {

        [$posts, $postTypes] = $this->getPosts($ids);
        $cover = $this->getCover($postTypes);
        $styles = $this->getThemeMods();
        $fonts = $this->getFonts($styles);

        if (!empty($posts)) {
            $html = render_blade_view('partials.content.pdf.layout', [
                'posts'     => $posts,
                'styles'    => $styles,
                'cover'     => $cover,
                'fonts'     => $fonts
            ]);

            $this->renderPdf($html);
        }
    }

    private function getPosts($ids) {
        $posts = [];
        $postTypes = [];
        if (!empty($ids) && is_array($ids)) {
            foreach ($ids as $id) {
                $post = get_post($id);
                if (!empty($post->post_status) && $post->post_status == 'publish') {
                    $post = \Municipio\Helper\Post::preparePostObject($post);
                    if (!empty($post->postType)) {
                        $postTypes[$post->postType] = $post->postType;
                    }
                    array_push($posts, $post);
                }
            }
        }
        return [$posts, $postTypes];
    }

    private function getCover($postTypes) {
        $postType = $this->defaultPrefix;
        if (count($postTypes) === 1) {
            $postType = current($postTypes);
        } 

        return $this->getCoverFieldsForPostType($postType);
    }

    private function getCoverFieldsForPostType($postType, $ranOnce = false) {
        $heading = get_field($postType . '_pdf_frontpage_heading', 'option');
        $introduction = get_field($postType . '_pdf_frontpage_introduction', 'option');
        $cover = Image::getImageAttachmentData(get_field($postType . '_pdf_frontpage_cover', 'option'), [800, 600]);
        $emblem = Image::getImageAttachmentData(get_field('pdf_frontpage_emblem', 'option'), [100, 100]);
        $defaultFrontpage = get_field($postType . '_pdf_fallback_frontpage', 'option');
        
        if ($postType != $this->defaultPrefix && empty($heading) && empty($introduction) && empty($cover)) {
            if (!$ranOnce) {
                if ($defaultFrontpage == 'none') {
                    return false;
                }
                // echo '<pre>' . print_r( $defaultFrontpage, true ) . '</pre>';die;
                return $this->getCoverFieldsForPostType($this->defaultPrefix, true);
            }
        }

        return [
            'heading'       => $heading,
            'introduction'  => $introduction,
            'cover'         => $cover,
            'emblem'        => $emblem,
        ];
    }

    private function getFonts($styles) {
        $args = array(
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => 'inherit',
            'post_mime_type' => 'font/ttf'
        );
        
        $customFonts = new \WP_Query($args);
        $heading = $styles['typography_heading'];
        $base = $styles['typography_base'];
        
        if (!empty($customFonts->posts) && (!empty($heading['font-family']) || $base['font-family']) && is_array($customFonts->posts)) {
            foreach ($customFonts->posts as $font) {
                if (!empty($font->post_title)) {
                    if ($font->post_title == $heading['font-family']) {
                        $heading['src'] = !empty($font->ID) ? wp_get_attachment_url( $font->ID ) : '';
                    }
                    
                    if ($font->post_title == $base['font-family']) {
                        $base['src'] = !empty($font->ID) ? wp_get_attachment_url( $font->ID ) : '';
                    } 
                }
            }
        }

        if (empty($base['src']) && !empty($base['font-family'])) {
            $base['google-font'] = $this->createGoogleFontImport($base['font-family']);
        }

        if (empty($heading['src']) && !empty($heading['font-family'])) {
            $heading['google-font'] = $this->createGoogleFontImport($heading['font-family']);
        }

        return [
            'base' => $base,
            'heading' => $heading
        ];
    }

    private function createGoogleFontImport($fontFamily) {
        return 'https://fonts.googleapis.com/css2?family=' . urlencode($fontFamily) . ':wght@100;300;400;500;600;700;800;900&display=swap';
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

