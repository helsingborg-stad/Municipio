<?php

namespace Municipio\Api\Pdf;

use Municipio\Helper\Image;

class PdfHelper
{    
    private $defaultPrefix = 'default';


    /**
     * Retrieves font information for heading and base styles.
     *
     * @param array $styles Typography styles.
     *
     * @return array Font information for heading and base styles.
     */
    public function getFonts($styles) {
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

    /**
     * Creates a Google Font import URL.
     *
     * @param string $fontFamily Font family name.
     *
     * @return string Google Font import URL.
     */
    private function createGoogleFontImport($fontFamily) {
        return 'https://fonts.googleapis.com/css2?family=' . urlencode($fontFamily) . ':wght@100;300;400;500;600;700;800;900&display=swap';
    }

    /**
     * Retrieves theme modifications.
     *
     * @return array Theme modifications.
     */
    public function getThemeMods() {
        return get_theme_mods();
    }

    /**
     * Retrieves cover information for the specified post types.
     *
     * @param array $postTypes Array of post types.
     *
     * @return array Cover information.
     */
    public function getCover(array $postTypes) {
        $postType = $this->defaultPrefix;
        if (!empty($postTypes)) {
            $postType = current($postTypes);
        } 

        return $this->getCoverFieldsForPostType($postType);
    }

    /**
     * Retrieves cover fields for the specified post type.
     *
     * @param string $postType Post type name.
     * @param bool   $ranOnce  Whether the method has already run once.
     *
     * @return array|false Cover fields or false if not found.
     */
    public function getCoverFieldsForPostType(string $postType = "", bool $ranOnce = false) {
        $heading = get_field($postType . '_pdf_frontpage_heading', 'option');
        $introduction = get_field($postType . '_pdf_frontpage_introduction', 'option');
        $cover = Image::getImageAttachmentData(get_field($postType . '_pdf_frontpage_cover', 'option'), [800, 600]);
        $emblem = Image::getImageAttachmentData(get_field('pdf_frontpage_emblem', 'option'), [100, 100]);
        if (!empty($heading) || !empty($introduction) || !empty($cover)) {
            return [
                'heading'       => $heading,
                'introduction'  => $introduction,
                'cover'         => $cover,
                'emblem'        => $emblem,
            ];
        }

        $defaultFrontpage = get_field($postType . '_pdf_fallback_frontpage', 'option');
        $copyFrontpageFrom = get_field($postType . '_pdf_custom_frontpage', 'option');
        
        if (!$ranOnce) {
            if ($defaultFrontpage == 'none') {
                return false;
            } 
            
            if ($defaultFrontpage == 'custom' && !empty($copyFrontpageFrom)) {
                return $this->getCoverFieldsForPostType($copyFrontpageFrom, true);
            }

            if ($defaultFrontpage == 'default') {
                return $this->getCoverFieldsForPostType($this->defaultPrefix, true);
            }
        }

        return false;
    }
}
