<?php

namespace Municipio\Api\Pdf;

use Municipio\Helper\Image;
use Municipio\Helper\FileConverters\FileConverterInterface;
use Municipio\Helper\S3 as S3Helper;


class PdfHelper implements PdfHelperInterface
{    
    private $defaultPrefix = 'default';

    /**
     * Retrieves font information for heading and base styles.
     *
     * @param array $styles Typography styles.
     *
     * @return array Font information for heading and base styles.
     */
    public function getFonts($styles, FileConverterInterface $fileConverter) {
        $args = array(
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => 'inherit',
            'post_mime_type' => 'application/font-woff'
        );
        
        $customFonts = new \WP_Query($args);

        [$heading, $base] = $this->getFontsFromCustomizer($styles);

        if (!empty($customFonts->posts) && (!empty($heading['font-family']) || $base['font-family']) && is_array($customFonts->posts)) {
            foreach ($customFonts->posts as $font) {
                if (!empty($font->post_title)) {
                    if (!empty($heading['font-family']) && $font->post_title == $heading['font-family']) {
                        $heading['src'] = $fileConverter::convert($font->ID);
                    }
                    
                    if (!empty($base['font-family']) && $font->post_title == $base['font-family']) {
                        $base['src'] = $fileConverter::convert($font->ID);
                        $base['variant'] = !empty($base['variant']) && $base['variant'] != 'regular' ? $base['variant'] : '400';
                    } 
                }
            }
        }

        return [
            'base' => $base,
            'heading' => $heading,
        ];
    }

    private function getFontsFromCustomizer($styles) {
        $heading = array_merge(
            [
                'font-family' => '',
                'variant' => '700',
                'src' => '',
            ], 
            $styles['typography_heading'] ?? []
        );

        $base = array_merge(
            [
                'font-family' => '',
                'variant' => '400',
                'src' => ''
            ], 
            $styles['typography_base'] ?? []
        );
        
        foreach ([&$heading, &$base] as $index => &$font) {
           $font['variant'] = intval($font['variant']);
            
           if (empty($font['variant'])) {
                $font['variant'] = $index == 0 ? '700' : '400';
           }
        }
        
        return [$heading, $base];
    }

    /**
     * Retrieves theme modifications.
     *
     * @return array Theme modifications.
     */
    public function getThemeMods() {
        $themeMods = function_exists('get_theme_mods') ? get_theme_mods() : [];
        return is_array($themeMods) ? $themeMods : [];
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
        $postTypes = !empty($postTypes) ? array_filter($postTypes, 'is_string') : [];
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
            if ($defaultFrontpage === 'custom' && !empty($copyFrontpageFrom)) {
                return $this->getCoverFieldsForPostType($copyFrontpageFrom, true);
            }

            if ($defaultFrontpage === 'default') {
                return $this->getCoverFieldsForPostType($this->defaultPrefix, true);
            }
        }

        return false;
    }

    public function systemHasSuggestedDependencies():bool {
        return extension_loaded('gd');
    }
}
