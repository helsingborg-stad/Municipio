<?php

namespace Municipio\Helper\User\FieldResolver;

interface UserGroupUrlInterface
{
    /**
     * Get the resolved URL.
     *
     * @return string|null
     */
    public function get(): ?string;

    /**
     * Resolve the arbitrary URL.
     *
     * @param string $termId
     * @return string|null
     */
    public function resolveArbitraryUrl(string $termId): ?string;

    /**
     * Resolve the post type URL.
     *
     * @param string $termId
     * @return string|null
     */
    public function resolvePostTypeUrl(string $termId): ?string;

    /**
     * Resolve the blog ID URL.
     *
     * @param string $termId
     * @return string|null
     */
    public function resolveBlogIdUrl(string $termId): ?string;
}