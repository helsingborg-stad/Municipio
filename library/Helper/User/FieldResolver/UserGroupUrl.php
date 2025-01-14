<?php

namespace Municipio\Helper\User\FieldResolver;

use Municipio\Helper\User\FieldResolver\UserGroupUrlInterface;
use WP_Term;

class UserGroupUrl implements UserGroupUrlInterface
{
    public function __construct(protected string $type, protected WP_Term $term, protected $acfService, protected $wpService, protected $userConfig)
    {
    }

    /**
     * Get the resolved URL.
     *
     * @return string|null
     */
    public function get(): ?string
    {
        $termId = $this->userConfig->getUserGroupTaxonomyName() . '_' . $this->term->term_id;

        return match ($this->type) {
            'arbitrary_url' => $this->resolveArbitraryUrl($termId),
            'post_type' => $this->resolvePostTypeUrl($termId),
            'blog_id' => $this->resolveBlogIdUrl($termId),
            default => null,
        };
    }

    /**
     * Resolve the arbitrary URL.
     *
     * @param string $termId
     * @return string|null
     */
    protected function resolveArbitraryUrl(string $termId): ?string
    {
        return $this->acfService->getField('arbitrary_url', $termId) ?: null;
    }

    /**
     * Resolve the post type URL.
     *
     * @param string $termId
     * @return string|null
     */
    protected function resolvePostTypeUrl(string $termId): ?string
    {
        $postObject = $this->acfService->getField('post_type', $termId);
        return $postObject && isset($postObject->ID) ? get_permalink($postObject->ID) : null;
    }

    /**
     * Resolve the blog ID URL.
     *
     * @param string $termId
     * @return string|null
     */
    protected function resolveBlogIdUrl(string $termId): ?string
    {
        $blogId = $this->acfService->getField('blog_id', $termId);
        if ($blogId) {
            $blogDetails = $this->wpService->getBlogDetails($blogId);
            return $blogDetails ? '//' . $blogDetails->domain . $blogDetails->path : null;
        }
        return null;
    }
}
