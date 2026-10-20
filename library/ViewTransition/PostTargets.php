<?php

declare(strict_types=1);

namespace Municipio\ViewTransition;

/**
 * Creates stable, document-safe names for a post's shared view-transition targets.
 */
class PostTargets
{
    /**
     * @return array<string, string>
     */
    public static function attributes(int $postId): array
    {
        $postId = abs($postId);

        return [
            'data-view-transition-target' => 'post',
            'style' => sprintf(
                '--municipio-view-transition-title: municipio-post-%d-title; --municipio-view-transition-image: municipio-post-%d-image;',
                $postId,
                $postId,
            ),
        ];
    }
}
