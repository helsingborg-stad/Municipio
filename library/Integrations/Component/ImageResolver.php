<?php

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageResolverInterface;
use Municipio\ImageConvert\TransparencyMetadata;

class ImageResolver implements ImageResolverInterface
{
    private const LQIP_WIDTH = 100;
    private const LQIP_HEIGHT = false;

    /**
     * Get image url
     *
     * @param int $id
     * @param array $size
     * @return string|null
     */
    public function getImageUrl(int $id, array $size): ?string
    {
        static $runtimeCache = [];
        $size = $this->normalizeSize($size);

        if ($this->isLqipRequest($size)) {
            return $this->resolveLqipUrl($id, $size);
        }

        $cacheKey = hash('sha256', $id . serialize($size));
        if (array_key_exists($cacheKey, $runtimeCache)) {
            return $runtimeCache[$cacheKey];
        }

        return $runtimeCache[$cacheKey] = $this->resolveAttachmentImageUrl($id, $size);
    }

    /**
     * Resolve the LQIP URL for an attachment.
     *
     * @param int $id
     * @param array $size
     * @return string|null
     */
    private function resolveLqipUrl(int $id, array $size): ?string
    {
        $attachmentMetadata = wp_get_attachment_metadata($id);
        $hasTransparency = $this->getStoredTransparencyState($attachmentMetadata);
        $lqipUrl = $hasTransparency === true ? null : $this->resolveAttachmentImageUrl($id, $size);

        return $this->filterLqipUrl(
            $lqipUrl,
            $id,
            $size,
            [
                'mimeType' => get_post_mime_type($id),
                'hasTransparency' => $hasTransparency,
            ],
        );
    }

    /**
     * Apply filterable LQIP overrides.
     *
     * @param string|null $lqipUrl
     * @param int $id
     * @param array $size
     * @param array $context
     * @return string|null
     */
    protected function filterLqipUrl(?string $lqipUrl, int $id, array $size, array $context): ?string
    {
        return apply_filters('Municipio/Component/Image/LqipUrl', $lqipUrl, $id, $size, $context);
    }

    /**
     * Resolve the image URL via WordPress.
     *
     * @param int $id
     * @param array $size
     * @return string|null
     */
    private function resolveAttachmentImageUrl(int $id, array $size): ?string
    {
        $image = wp_get_attachment_image_src($id, $size);

        if ($image !== false && isset($image[0]) && filter_var($image[0], FILTER_VALIDATE_URL)) {
            return $image[0];
        }

        return null;
    }

    /**
     * Read the stored transparency decision from attachment metadata.
     *
     * @param mixed $attachmentMetadata
     * @return bool|null
     */
    private function getStoredTransparencyState(mixed $attachmentMetadata): ?bool
    {
        if (
            !is_array($attachmentMetadata)
            || !array_key_exists(TransparencyMetadata::ATTACHMENT_METADATA_KEY, $attachmentMetadata)
        ) {
            return null;
        }

        return (bool) $attachmentMetadata[TransparencyMetadata::ATTACHMENT_METADATA_KEY];
    }

    /**
     * Determine whether the requested size is the component library LQIP size.
     *
     * @param array $size
     * @return bool
     */
    private function isLqipRequest(array $size): bool
    {
        return ($size[0] ?? null) === self::LQIP_WIDTH
            && ($size[1] ?? null) === self::LQIP_HEIGHT;
    }

    /**
     * Normalize size values so runtime cache keys are stable.
     *
     * @param array $size
     * @return array
     */
    private function normalizeSize(array $size): array
    {
        return array_map(
            static fn(mixed $value): mixed => $value === 0 ? false : $value,
            $size,
        );
    }

    /**
     * Get image alt
     *
     * @param int $id
     * @return string|null
     */
    public function getImageAltText(int $id): ?string
    {
        $alt = get_post_meta($id, '_wp_attachment_image_alt', true);
        if ($alt) {
            return $alt;
        }

        return null;
    }
}
