<?php

declare(strict_types=1);

namespace Municipio\Content;

/**
 * Shares filtered post content between content APIs during the current request.
 *
 * This compatibility cache can be removed when the legacy post object has been
 * completely replaced and content is no longer filtered through both APIs.
 */
final class FilteredContentRuntimeCache
{
    private static array $cache = [];

    /**
     * Return an existing filtered value or create it once for this context.
     */
    public static function remember(
        int $blogId,
        int $postId,
        string $content,
        callable $filter
    ): string {
        $cacheKey = implode(':', [$blogId, $postId, md5($content)]);

        if (!array_key_exists($cacheKey, self::$cache)) {
            self::$cache[$cacheKey] = $filter($content);
        }

        return self::$cache[$cacheKey];
    }

    /**
     * Clear request-local values. Primarily useful for isolated tests.
     */
    public static function clear(): void
    {
        self::$cache = [];
    }
}
