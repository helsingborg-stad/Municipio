<?php

namespace Municipio\Api\Pdf;

use Municipio\Helper\Image;
use Municipio\Helper\FileConverters\FileConverterInterface;
use Municipio\Helper\S3 as S3Helper;

/**
 * Class PdfHelper
 */
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
    public function getFonts($styles, FileConverterInterface $fileConverter)
    {
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
                        $heading['src'] = $this->checkForTTF($font->ID);
                    }

                    if (!empty($base['font-family']) && $font->post_title == $base['font-family']) {
                        $base['src']     = $this->checkForTTF($font->ID);
                        $base['variant'] = $this->handleFontVariants($base);
                    }
                }
            }
        }

        $googleFontString = $this->createGoogleFontString([$base, $heading]);

        return [
            'googleFonts' => $googleFontString,
            'base'        => $base,
            'heading'     => $heading,
        ];
    }

    /**
     * Creates a string with the google fonts
     *
     * @param array $fonts
     * @return string
     */
    private function createGoogleFontString(array $fonts): string
    {

        $str = "";
        foreach ($fonts as $font) {
            if (!empty($font['font-family'])) {
                $str .= 'family=' . str_replace(' ', '+', $font['font-family']) .
                ':ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&';
            }
        }

        if (!empty($str)) {
            $str = 'https://fonts.googleapis.com/css2?' . $str . '&display=swap';
        }

        return $str;
    }

    /**
     * Sets correct font variant.
     *
     * @param array $font
     * @return string
     */
    private function handleFontVariants(array $font): string
    {
        return !empty($font['variant']) && $font['variant'] != 'regular' ? $font['variant'] : '400';
    }

    /**
     * Gets ttf meta for a post.
     *
     * @param int $id Id of a post.
     * @return false|string
     */
    private function checkForTTF(int $id)
    {
        $ttfMeta = get_post_meta($id, 'ttf', true);

        return !empty($ttfMeta) ? $ttfMeta : false;
    }

     /**
     * Gets typography settings from the customizer
     *
     * @param array $styles All the customizer styles.
     * @return array
     */
    private function getFontsFromCustomizer(array $styles): array
    {
        $heading = array_merge(
            [
                'font-family' => '',
                'variant'     => '700',
                'src'         => '',
            ],
            $styles['typography_heading'] ?? []
        );

        $base = array_merge(
            [
                'font-family' => '',
                'variant'     => '400',
                'src'         => ''
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
    public function getThemeMods()
    {
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
    public function getCover(array $postTypes)
    {
        $postType  = $this->defaultPrefix;
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
    public function getCoverFieldsForPostType(string $postType = "", bool $ranOnce = false)
    {
        $heading      = get_field($postType . '_pdf_frontpage_heading', 'option');
        $introduction = get_field($postType . '_pdf_frontpage_introduction', 'option');
        $cover        = Image::getImageAttachmentData(get_field($postType . '_pdf_frontpage_cover', 'option'), [800, 600]);
        $emblem       = Image::getImageAttachmentData(get_field('pdf_frontpage_emblem', 'option'), [100, 100]);
        if (!empty($heading) || !empty($introduction) || !empty($cover)) {
            return [
                'heading'      => $heading,
                'introduction' => $introduction,
                'cover'        => $cover,
                'emblem'       => $emblem,
            ];
        }

        $defaultFrontpage  = get_field($postType . '_pdf_fallback_frontpage', 'option');
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

     /**
     * Checks for system dependencies
     *
     * @return bool
     */
    public function systemHasSuggestedDependencies(): bool
    {
        return extension_loaded('gd');
    }
}
