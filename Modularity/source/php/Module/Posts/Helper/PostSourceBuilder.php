<?php

namespace Modularity\Module\Posts\Helper;

use Municipio\Helper\User\User;
use WpService\WpService;

class PostSourceBuilder
{
    public function __construct(
        private WpService $wpService,
        private User $userHelper,
        private array $fields = [],
    ) {}

    /**
     * Get original post sources from fields.
     */
    public function getOriginalSources(): array
    {
        return $this->fields['posts_data_network_sources'] ?? [];
    }

    /**
     * Get user group source blog ID if set.
     */
    public function getUserGroupSourceBlogId(): null|string
    {
        $userGroupBlogId = !empty($this->fields['posts_data_get_posts_from_user_group'])
            ? $this->userHelper->getUserGroupOriginalBlogId()
            : null;

        return !empty($userGroupBlogId) ? (string) $userGroupBlogId : null;
    }

    /**
     * Get blog name from blog ID.
     */
    public function getBlogNameFromBlogId(string $blogId): array
    {
        $blogDetails = $this->wpService->getBlogDetails($blogId);

        return [
            'value' => $blogId,
            'label' => $blogDetails->blogname ?? $this->wpService->__('Unknown Blog', 'municipio'),
        ];
    }

    /**
     * Get all post sources including user group source if applicable.
     */
    public function getSources(): array
    {
        $sources = $this->getOriginalSources();
        $userGroupSourceBlogId = $this->getUserGroupSourceBlogId();

        $userGroupAlreadyExists = !empty(array_filter($sources, function ($source) use ($userGroupSourceBlogId) {
            return $source['value'] === $userGroupSourceBlogId;
        }));

        if ($userGroupSourceBlogId && !$userGroupAlreadyExists) {
            $sources[] = $this->getBlogNameFromBlogId($userGroupSourceBlogId);
        }

        return $sources;
    }
}
