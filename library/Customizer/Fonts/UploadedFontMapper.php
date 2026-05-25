<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use WpService\WpService;

/**
 * Maps upload field values and attachments to font definitions.
 */
class UploadedFontMapper
{
    /**
     * @param WpService $wpService
     */
    public function __construct(private readonly WpService $wpService)
    {
    }

    /**
     * Maps an attachment to a font definition.
     *
     * @param int $attachmentId
     * @param string|null $fontName
     *
     * @return array{id: int, name: string, type: string, url: string}|null
     */
    public function fromAttachment(int $attachmentId, ?string $fontName = null): ?array
    {
        if ($attachmentId === 0) {
            return null;
        }

        $url = $this->wpService->wpGetAttachmentUrl($attachmentId);

        if ($url === false) {
            return null;
        }

        $fileType = $this->wpService->wpCheckFiletypeAndExt(
            $url,
            basename($url),
        );

        $resolvedFontName = is_string($fontName) && $fontName !== ''
            ? $fontName
            : $this->deriveFontNameFromFilePath($url);

        return [
            'id'   => $attachmentId,
            'name' => $resolvedFontName,
            'type' => $fileType['ext'] ?? pathinfo(basename($url), PATHINFO_EXTENSION),
            'url'  => $url,
        ];
    }

    /**
     * Maps a raw upload field value to a font definition.
     *
     * @param int|string $file
     * @param string|null $fontName
     *
     * @return array{id: int, name: string, type: string, url: string}|null
     */
    public function fromUploadValue(int|string $file, ?string $fontName = null): ?array
    {
        if (is_numeric($file)) {
            return $this->fromAttachment((int) $file, $fontName);
        }

        if (!is_string($file) || $file === '') {
            return null;
        }

        $extension = pathinfo(basename($file), PATHINFO_EXTENSION);
        $resolvedFontName = is_string($fontName) && $fontName !== ''
            ? $fontName
            : $this->deriveFontNameFromFilePath($file);

        return [
            'id'   => 0,
            'name' => $resolvedFontName,
            'type' => $extension !== '' ? $extension : 'woff',
            'url'  => $file,
        ];
    }

    /**
     * Derives a readable font family name from an uploaded file path.
     *
     * @param string $filePath
     *
     * @return string
     */
    private function deriveFontNameFromFilePath(string $filePath): string
    {
        $stem = pathinfo(basename($filePath), PATHINFO_FILENAME);
        $stem = str_replace(['-', '_'], ' ', $stem);
        $stem = trim($stem);

        if ($stem === '') {
            return $this->wpService->__('Untitled Font', 'municipio');
        }

        return ucwords($stem);
    }
}
