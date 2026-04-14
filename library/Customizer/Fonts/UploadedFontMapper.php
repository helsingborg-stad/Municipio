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
     * @param string $fontName
     *
     * @return array{id: int, name: string, type: string, url: string}|null
     */
    public function fromAttachment(int $attachmentId, string $fontName): ?array
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

        return [
            'id'   => $attachmentId,
            'name' => $fontName,
            'type' => $fileType['ext'] ?? pathinfo(basename($url), PATHINFO_EXTENSION),
            'url'  => $url,
        ];
    }

    /**
     * Maps a raw upload field value to a font definition.
     *
     * @param int|string $file
     * @param string $fontName
     *
     * @return array{id: int, name: string, type: string, url: string}|null
     */
    public function fromUploadValue(int|string $file, string $fontName): ?array
    {
        if (is_numeric($file)) {
            return $this->fromAttachment((int) $file, $fontName);
        }

        if (!is_string($file) || $file === '') {
            return null;
        }

        return [
            'id'   => 0,
            'name' => $fontName,
            'type' => pathinfo(basename($file), PATHINFO_EXTENSION) !== '' ? pathinfo(basename($file), PATHINFO_EXTENSION) : 'woff',
            'url'  => $file,
        ];
    }
}
