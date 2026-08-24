<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin;

use WpService\WpService;

/**
 * Provides the per-post control for excluding public content from indexing.
 */
class ExcludeFromSearch
{
    private const EXCLUDED_META_KEY = 'exclude_from_search';

    public function __construct(private WpService $wpService) {}

    /**
     * Register post editor hooks.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('post_submitbox_misc_actions', [$this, 'render']);
        $this->wpService->addAction('attachment_submitbox_misc_actions', [$this, 'render']);
        $this->wpService->addAction('save_post', [$this, 'save']);
    }

    /**
     * Render the post editor checkbox for public, searchable post types.
     */
    public function render(): void
    {
        $post = $this->wpService->getPost();

        if (!$post instanceof \WP_Post || !$this->isIndexablePostType($post)) {
            return;
        }

        $checked = $this->wpService->getPostMeta($post->ID, self::EXCLUDED_META_KEY, true) ? ' checked' : '';
        echo '<div class="misc-pub-section misc-pub-index"><label><input type="hidden" value="false" name="exclude-from-search"><input type="checkbox" name="exclude-from-search" value="true"' . $checked . '> ' . esc_html($this->wpService->__('Exclude from search', 'municipio')) . '</label></div>';
    }

    /**
     * Persist the exclusion setting from the post editor.
     */
    public function save(int $postId): void
    {
        $value = $_POST['exclude-from-search'] ?? null;

        if ($value === 'true') {
            $this->wpService->updatePostMeta($postId, self::EXCLUDED_META_KEY, true);
            return;
        }

        if ($value === 'false') {
            $this->wpService->deletePostMeta($postId, self::EXCLUDED_META_KEY);
        }
    }

    /**
     * Check that the post type is public and included in WordPress search.
     */
    private function isIndexablePostType(\WP_Post $post): bool
    {
        return in_array($this->wpService->getPostType($post), $this->wpService->getPostTypes([
            'public' => true,
            'exclude_from_search' => false,
        ]), true);
    }
}