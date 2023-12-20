<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Helper\ResourceFromApiHelper;
use Municipio\Helper\WP;

/**
 * Class ModifyWpGetAttachmentImageSrc
 */
class ModifyWpGetAttachmentImageSrc
{
    private ModifiersHelperInterface $modifiersHelper;

    /**
     * Class constructor.
     *
     * @param ModifiersHelperInterface $modifiersHelper The modifiers helper.
     */
    public function __construct(ModifiersHelperInterface $modifiersHelper)
    {
        $this->modifiersHelper = $modifiersHelper;
    }

    /**
     * Handle the image attachment.
     *
     * @param mixed $image The image.
     * @param int $attachmentId The attachment ID.
     * @param string $size The size of the image.
     * @param bool $icon Whether the image is an icon.
     * @return mixed The modified image.
     */
    public function handle($image, $attachmentId, $size, $icon)
    {
        if (!empty($image) || !is_numeric($attachmentId) || (int)$attachmentId > -1) {
            return $image;
        }

        $resource = $this->modifiersHelper->getResourceFromPostId($attachmentId);

        if (empty($resource)) {
            return $image;
        }

        $attachment = WP::getPost($attachmentId);

        if (!is_a($attachment, 'WP_Post')) {
            return $image;
        }

        if (!isset($attachment->meta->media_details) || !isset($attachment->meta->media_details->sizes)) {
            return [$attachment->meta->source_url];
        }

        $matchingSize = ResourceFromApiHelper::getClosestImageBySize($size, $attachment->meta->media_details->sizes);

        if (empty($matchingSize)) {
            return [
                $attachment->meta->source_url,
                $attachment->meta->width ?? null,
                $attachment->meta->height ?? null,
                false
            ];
        }

        $image = [
            $attachment->meta->media_details->sizes->{$matchingSize}->source_url ?? $attachment->meta->source_url,
            $attachment->meta->media_details->sizes->{$matchingSize}->width ?? $attachment->meta->width ?? null,
            $attachment->meta->media_details->sizes->{$matchingSize}->height ?? $attachment->meta->height ?? null,
            true
        ];

        return $image;
    }
}
