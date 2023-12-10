<?php

namespace Municipio\Api\Pdf;

use Municipio\Helper\Image;
use Municipio\Helper\WoffConverter as WoffConverterHelper;
use Municipio\Helper\S3 as S3Helper;

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

    /**
     * Converts a WOFF font file to TTF format.
     *
     * This function takes a font ID, retrieves the WOFF font file path, and performs the conversion
     * to TTF format. If S3 support is available and the file is on S3, it handles download,
     * conversion, and upload operations. If the file is local, it directly converts it to TTF.
     *
     * @param int $fontId The font ID to convert.
     * @return string The path or S3 key of the converted TTF font file, or an empty string if unsuccessful.
     */
    private function convertWOFFToTTF($fontId) {
        $woffFontFile = get_attached_file($fontId);
        
        if ($this->isValidWoffFontFile($woffFontFile)) {
            if (S3Helper::hasS3Support() && S3Helper::isS3Path($woffFontFile)) {

                $ttfFontFile = $this->createVariantName(
                    $woffFontFile,
                    "ttf"
                );

                if ($s3TtfKey = S3Helper::objectExistsOnS3($ttfFontFile)) {
                    return $s3TtfKey;
                }
    
                // Download the file from S3 to a temporary local file
                $tempLocalFile = tempnam(sys_get_temp_dir(), 'woff_download_');
                S3Helper::downloadFromS3(
                    $woffFontFile, 
                    $tempLocalFile
                );
    
                // Convert the local WOFF file to TTF
                $localTtfFile = $this->convertLocalWoffToTtf($tempLocalFile);
    
                // Upload the TTF file to S3
                S3Helper::uploadToS3($localTtfFile, $ttfFontFile);
    
                unlink($tempLocalFile);
    
                return $ttfFontFile;
            } else {
                return $this->convertLocalWoffToTtf($woffFontFile);
            }
        }
    
        return "";
    }
    
    /**
     * Converts a local WOFF font file to TTF format.
     *
     * This function utilizes the WoffConverterHelper to perform the conversion
     * and generates a TTF variant name using the createVariantName method.
     *
     * @param string $woffFontFile The path to the local WOFF font file.
     * @return string The path to the converted TTF font file.
     */
    private function convertLocalWoffToTtf($woffFontFile) {
        WoffConverterHelper::convert(
            $woffFontFile, 
            $this->createVariantName($woffFontFile, "ttf")
        );
        return $this->createVariantName($woffFontFile, "ttf"); 
    }

    /**
     * Checks if a file is a valid WOFF font file.
     *
     * This function verifies the existence of the file, its non-empty status,
     * and whether its MIME type is 'application/font-woff'.
     *
     * @param string $fontFile The path to the font file being checked.
     * @return bool True if the file is a valid WOFF font, false otherwise.
     */
    private function isValidWoffFontFile($fontFile) {
        return !empty($fontFile) && file_exists($fontFile) && mime_content_type($fontFile) == 'application/font-woff';
    }

    /**
     * Creates a variant file name based on the provided file name and target suffix.
     *
     * This function extracts the filename from the given path, appends the specified
     * target suffix, and returns the new file name.
     *
     * @param string $fileName      The original file name or path.
     * @param string $targetSuffix  The target suffix to append to the filename.
     * @return string The new variant file name.
     */
    private function createVariantName($fileName, $targetSuffix) {
        $pathInfo = pathinfo($fileName);
        $newFileName = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $targetSuffix;
        return $newFileName;
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
