<?php

namespace Municipio\Api\Customizer;

use Municipio\Api\RestApiEndpoint;
use Municipio\Helper\WpService as WpServiceHelper;
use Municipio\Upgrade\V42\InteractsWithNativeFontLibrary;
use WP_Error;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WpService\WpService;

/**
 * Downloads a font-face variant from a Design Library import and activates it in the native Font Library.
 */
class DesignLibraryFontActivation extends RestApiEndpoint
{
    use InteractsWithNativeFontLibrary;

    private const NAMESPACE = 'municipio/v1';
    private const ROUTE = 'design-library/activate-font';

    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => [$this, 'permissionCallback'],
            'args' => [
                'fontFamily' => [
                    'description' => __('Font family name to activate.', 'municipio'),
                    'type' => 'string',
                    'required' => true,
                ],
                'fontWeight' => [
                    'description' => __('Font weight of the face to activate.', 'municipio'),
                    'type' => 'string',
                    'required' => false,
                    'default' => '400',
                ],
                'fontStyle' => [
                    'description' => __('Font style of the face to activate.', 'municipio'),
                    'type' => 'string',
                    'required' => false,
                    'default' => 'normal',
                ],
                'url' => [
                    'description' => __('Remote URL of the font file to download and activate.', 'municipio'),
                    'type' => 'string',
                    'format' => 'uri',
                    'required' => true,
                ],
            ],
        ]);
    }

    /**
     * Restrict font activation to authenticated users with Customizer capability.
     */
    public function permissionCallback(): bool
    {
        return is_user_logged_in() && current_user_can('customize');
    }

    /**
     * @return WP_REST_Response|WP_Error
     */
    public function handleRequest(WP_REST_Request $request)
    {
        $fontFamily = (string) $request->get_param('fontFamily');
        $fontWeight = (string) $request->get_param('fontWeight');
        $fontStyle = (string) $request->get_param('fontStyle');
        $url = (string) $request->get_param('url');

        if (trim($fontFamily) === '' || trim($url) === '') {
            return new WP_Error(
                'municipio_font_activation_missing_params',
                __('Missing font family or file URL.', 'municipio'),
                ['status' => WP_Http::BAD_REQUEST],
            );
        }

        if (!$this->nativeFontLibraryIsAvailable()) {
            return new WP_Error(
                'municipio_font_activation_unavailable',
                __('The native font library is not available on this site.', 'municipio'),
                ['status' => WP_Http::NOT_IMPLEMENTED],
            );
        }

        $fontFamilyPostId = $this->createNativeFontFamilyIfMissing($fontFamily);

        if ($fontFamilyPostId === null) {
            return new WP_Error(
                'municipio_font_activation_family_failed',
                __('Unable to create or find the font family.', 'municipio'),
                ['status' => WP_Http::INTERNAL_SERVER_ERROR],
            );
        }

        $downloadedFontFile = $this->downloadFontFile($url);

        if ($downloadedFontFile === null) {
            return new WP_Error(
                'municipio_font_activation_download_failed',
                __('Unable to download the font file.', 'municipio'),
                ['status' => WP_Http::BAD_GATEWAY],
            );
        }

        $this->createNativeFontFaceIfMissing(
            $fontFamilyPostId,
            $fontFamily,
            $downloadedFontFile['url'],
            $fontStyle !== '' ? $fontStyle : 'normal',
            $fontWeight !== '' ? $fontWeight : '400',
            $downloadedFontFile['fontFile'],
        );

        return rest_ensure_response([
            'success' => true,
            'fontFamilyId' => $fontFamilyPostId,
        ]);
    }

    /**
     * Downloads a remote font file into WordPress' native font uploads directory.
     *
     * @return array{url: string, fontFile: string}|null
     */
    private function downloadFontFile(string $url): ?array
    {
        $this->ensureDownloadUrlIsAvailable();

        $wpService = $this->getWpService();
        $temporaryFile = $wpService->downloadUrl($url);

        if ($wpService->isWpError($temporaryFile) || !is_string($temporaryFile) || $temporaryFile === '') {
            return null;
        }

        $file = [
            'name' => $this->getDownloadedFontFileName($url, $temporaryFile),
            'tmp_name' => $temporaryFile,
            'error' => 0,
            'size' => (int) filesize($temporaryFile),
        ];

        $overrides = ['test_form' => false];

        if (class_exists(\WP_Font_Utils::class) && method_exists(\WP_Font_Utils::class, 'get_allowed_font_mime_types')) {
            $overrides['mimes'] = \WP_Font_Utils::get_allowed_font_mime_types();
        }

        $wpService->addFilter('upload_dir', '_wp_filter_font_directory');
        $sideloadedFile = $wpService->wpHandleSideload($file, $overrides);
        $wpService->removeFilter('upload_dir', '_wp_filter_font_directory');

        if (!is_array($sideloadedFile) || empty($sideloadedFile['file']) || empty($sideloadedFile['url'])) {
            if (file_exists($temporaryFile)) {
                unlink($temporaryFile);
            }

            return null;
        }

        return [
            'url' => (string) $sideloadedFile['url'],
            'fontFile' => $this->relativeFontsPath((string) $sideloadedFile['file']),
        ];
    }

    private function getDownloadedFontFileName(string $source, string $temporaryFile): string
    {
        $path = (string) parse_url($source, PHP_URL_PATH);
        $fileName = basename($path);

        if ($fileName !== '' && $fileName !== '/' && $fileName !== '.') {
            return $fileName;
        }

        $extension = pathinfo($temporaryFile, PATHINFO_EXTENSION);

        return $extension !== '' ? 'font.' . $extension : 'font-file';
    }

    private function relativeFontsPath(string $path): string
    {
        $fontDir = $this->getWpService()->wpGetFontDir();
        $baseDir = isset($fontDir['basedir']) && is_string($fontDir['basedir']) ? rtrim($fontDir['basedir'], '/') : '';

        if ($baseDir !== '' && str_starts_with($path, $baseDir)) {
            return ltrim(substr($path, strlen($baseDir)), '/');
        }

        return $path;
    }

    private function ensureDownloadUrlIsAvailable(): void
    {
        if (function_exists('download_url') || !defined('ABSPATH')) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    protected function getWpService(): WpService
    {
        return WpServiceHelper::get();
    }
}
