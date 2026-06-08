<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize;

/**
 * Resolves the design-builder scope name for a post type.
 */
class ResolvePostTypeScope
{
    /**
     * Resolves a data-scope value for a post type.
     *
     * @param string|null $postType The current post type.
     * @param bool $isSingle True when the current request is a single post view.
     * @param bool $isArchive True when the current request is an archive view.
     *
     * @return string|null A semicolon-terminated scope value, or null when no valid post type exists.
     */
    public function resolve(?string $postType, bool $isSingle = false, bool $isArchive = false): ?string
    {
        $sanitizedPostType = $this->sanitizePostType($postType);

        if ($sanitizedPostType === null) {
            return null;
        }

        $scopes = [sprintf('s-post-type-%s', $sanitizedPostType)];

        if ($isSingle) {
            $scopes[] = sprintf('s-post-type-%s-single', $sanitizedPostType);
        }

        if ($isArchive) {
            $scopes[] = sprintf('s-post-type-%s-archive', $sanitizedPostType);
        }

        return implode('; ', $scopes) . ';';
    }

    /**
     * Sanitizes a post type before building a scope name.
     *
     * @param string|null $postType The current post type.
     *
     * @return string|null A sanitized post type, or null when no valid value exists.
     */
    private function sanitizePostType(?string $postType): ?string
    {
        $postType = is_string($postType) ? trim($postType) : '';

        if ($postType === '') {
            return null;
        }

        $sanitizedPostType = preg_replace('/[^A-Za-z0-9_-]+/', '-', $postType);
        $sanitizedPostType = is_string($sanitizedPostType) ? trim($sanitizedPostType, '-') : '';

        if ($sanitizedPostType === '') {
            return null;
        }

        return $sanitizedPostType;
    }
}
