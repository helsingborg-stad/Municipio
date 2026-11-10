<?php

declare(strict_types=1);

namespace Municipio\Customizer\Controls\Upload;

use WP_Customize_Upload_Control;

/**
 * Upload control that still previews the stored file when its url cannot be
 * resolved to an attachment on the current site (for example network media,
 * CDN urls or values set by migrations).
 */
class UploadControl extends WP_Customize_Upload_Control
{
    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @return void
     */
    public function to_json(): void
    {
        parent::to_json();

        if (($this->json['attachment'] ?? null) !== null) {
            return;
        }

        $value = $this->value();

        if (!is_string($value) || trim($value) === '') {
            return;
        }

        $this->json['attachment'] = self::createFallbackAttachment($value);
    }

    /**
     * Build a minimal attachment model for a url that is not in the media library.
     *
     * @param string $url Stored file url.
     *
     * @return array<string, mixed>
     */
    public static function createFallbackAttachment(string $url): array
    {
        $path = (string) parse_url($url, PHP_URL_PATH);
        $fileType = wp_check_filetype(basename($path));
        $mime = is_string($fileType['type'] ?? null) ? $fileType['type'] : '';
        $type = $mime !== '' ? strtok($mime, '/') : 'document';
        $title = basename($path) !== '' ? basename($path) : $url;

        return [
            // A truthy id is required by the core template to render the preview.
            'id' => -1,
            'url' => $url,
            'type' => $type,
            'subtype' => $mime !== '' ? substr($mime, strlen($type) + 1) : '',
            'mime' => $mime,
            'title' => $title,
            'filename' => $title,
            'icon' => wp_mime_type_icon($type),
            'sizes' => $type === 'image' ? ['full' => ['url' => $url]] : [],
        ];
    }
}
