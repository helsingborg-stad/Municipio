<?php

namespace Municipio\Api\Pdf;

use Municipio\Helper\Image;
use Municipio\Helper\WoffConverter as WoffConverterHelper;

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
            'post_mime_type' => 'application/font-woff'
        );
        
        $customFonts = new \WP_Query($args);
        $heading = $styles['typography_heading'];
        $base = $styles['typography_base'];
        
        if (!empty($customFonts->posts) && (!empty($heading['font-family']) || $base['font-family']) && is_array($customFonts->posts)) {
            foreach ($customFonts->posts as $font) {
                if (!empty($font->post_title)) {
                    if (!empty($heading['font-family']) && $font->post_title == $heading['font-family']) {
                        $heading['src'] = $this->convertWOFFToTTF($font->ID);
                    }
                    
                    if (!empty($base['font-family']) && $font->post_title == $base['font-family']) {
                        $base['src'] = $this->convertWOFFToTTF($font->ID);
                    } 
                }
            }
        }

        $downloadedFontFiles = get_option('kirki_downloaded_font_files');
        $fontFacesString = "";
                
        if (empty($base['src']) && !empty($base['font-family']) && !empty($downloadedFontFiles) && is_array($downloadedFontFiles)) {
            $baseUrl = $this->createGoogleFontImport($base['font-family']);
            $fontFacesString .= $this->buildFontFaces($baseUrl, $downloadedFontFiles);
        }

        if (empty($heading['src']) && !empty($heading['font-family']) && !empty($downloadedFontFiles) && is_array($downloadedFontFiles)) {
            $headingUrl = $this->createGoogleFontImport($heading['font-family']);
            $fontFacesString .= $this->buildFontFaces($headingUrl, $downloadedFontFiles);
        }

        return [
            'base' => $base,
            'heading' => $heading,
            'localGoogleFonts' => $fontFacesString,
        ];
    }

    private function convertWOFFToTTF($fontId) {
        $fontFile = get_attached_file($fontId);
        if (!empty($fontFile)) {
            if (!empty($fontFile) && file_exists($fontFile) && mime_content_type($fontFile) == 'application/font-woff') {
                WoffConverterHelper::convert($fontFile, str_replace('.woff', '.ttf', $fontFile ));
                return str_replace('.woff', '.ttf',wp_get_attachment_url( $fontId ));
            }
        }

        return "";
    }

    private function buildFontFaces ($url, $downloadedFontFiles) {
        $fontFacesString = "";

        if (ini_get('allow_url_fopen')) {     
            $response = wp_remote_get( $url, array( 'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8' ) );
            
            if ( is_wp_error( $response ) ) {
                return [];
            }

            $contents = wp_remote_retrieve_body( $response );
            
            if (!empty($contents) && is_string($contents)) {
                foreach ($downloadedFontFiles as $key => $fontFile) {
                    $contents = str_replace($key, $fontFile, $contents);
                }

                $fontFaces = explode('@font-face', $contents);

                foreach ($fontFaces as $fontFace) {
                    if (!preg_match('/fonts.gstatic/', $fontFace) && preg_match('/src:/', $fontFace)) {
                        $fontFacesString .= '@font-face ' . $fontFace;
                    }
                }
            }
            
            return $fontFacesString;
        }
    }
 
    /**
     * Creates a Google Font import URL.
     *
     * @param string $fontFamily Font family name.
     *
     * @return string Google Font import URL.
     */
    private function createGoogleFontImport($fontFamily) {
        return 'https://fonts.googleapis.com/css?family=' . urlencode($fontFamily) . ':100,300,400,500,600,700,800,900&display=swap';
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
