<?php

namespace Municipio\ImageFocus\Storage;

use WpService\WpService;

class FocusPointStorage
{
    private const META_KEY = '_focus_point';

    public function __construct(private WpService $wpService) {}

    public function get(int $attachmentId): ?array
    {
        $meta = $this->wpService->getPostMeta($attachmentId, self::META_KEY, true);
        if (is_string($meta) && !empty($meta)) {
            $meta = json_decode($meta, true);
        }

        if(is_array($meta)) {
            $filter = array_filter($meta, function($value) {
                return is_numeric($value) && $value >= 0 && $value <= 100;
            });

            if(count($filter) === 2 && array_key_exists('left', $meta) && array_key_exists('top', $meta)) {
                return $meta;
            }
        }

        return null;
    }

    public function set(int $attachmentId, array $focus): bool
    {
        return (bool) $this->wpService->updatePostMeta(
            $attachmentId,
            self::META_KEY,
            json_encode($focus)
        );
    }
}