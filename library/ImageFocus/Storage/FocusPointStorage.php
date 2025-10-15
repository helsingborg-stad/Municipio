<?php

namespace Municipio\ImageFocus\Storage;

use WpService\WpService;

class FocusPointStorage
{
    private const META_KEY = '_focus_point';
    private const MANUAL_META_KEY = '_manual_focus_point';

    public function __construct(private WpService $wp) {}

    public function get(int $attachmentId): ?array
    {
        $meta = $this->wp->getPostMeta($attachmentId, self::META_KEY, true);
        return is_array($meta) ? $meta : null;
    }

    public function set(int $attachmentId, array $focus): bool
    {
        return (bool) $this->wp->updatePostMeta(
            $attachmentId,
            self::META_KEY,
            json_encode($focus)
        );
    }

    public function getManual(int $attachmentId): ?array
    {
        $meta = $this->wp->getPostMeta($attachmentId, self::MANUAL_META_KEY, true);
        return is_array($meta) ? $meta : null;
    }
}