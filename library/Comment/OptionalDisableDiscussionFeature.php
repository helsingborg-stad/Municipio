<?php

namespace Municipio\Comment;

use AcfService\Contracts\GetField;
use Municipio\HooksRegistrar\Hookable;
use WP_Admin_Bar;
use WpService\WpService;

/**
 * Optional disable discussion feature.
 */
class OptionalDisableDiscussionFeature implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private WpService $wpService, private GetField $acfService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'addHooksConditional'], 1);
        $this->wpService->addAction('wp_enqueue_scripts', [$this, 'addCommentReplyScript'], 1);
    }

    /**
     * Enqueue comment-reply script if needed
     * 
     * @return void
     */
    private function addCommentReplyScript(): void
    {
        if (
            $this->isDisabled() ||
            !$this->wpService->isSingular() ||
            !$this->wpService->commentsOpen() ||
            !$this->wpService->getOption('thread_comments')
        ) {
            return;
        }

        $this->wpService->wpEnqueueScript('comment-reply');
    }

    /**
     * Adds hooks conditionally depending on the settings.
     * 
     * @return void
     */
    public function addHooksConditional(): void
    {
        if (!$this->isDisabled()) {
            return;
        }

        $this->wpService->addFilter('render_block', [$this, 'preventLatestCommentsBlockFromRendering'], 10, 2);
        $this->wpService->addAction('admin_init', [$this, 'disableSupportForPostTypes']);
        $this->wpService->addAction('admin_init', [$this, 'redirectUserIfOnEditCommentsPage']);
        $this->wpService->addAction('admin_init', [$this, 'redirectUserIfOnSettingsPage']);
        $this->wpService->addAction('admin_init', [$this, 'removeMetaBox']);
        $this->wpService->addAction('admin_menu', [$this, 'removeSettingsPage']);
        $this->wpService->addAction('admin_menu', [$this, 'removeFromAdminMenu']);
        $this->wpService->addAction('admin_bar_menu', [$this, 'removeFromAdminBar'], 61);
        $this->wpService->addAction('widgets_init', [$this, 'disableWidget']);

        // Close comments on frontend
        $this->wpService->addFilter('comments_open', '__return_false', 20, 2);
        $this->wpService->addFilter('pings_open', '__return_false', 20, 2);
        $this->wpService->addFilter('comments_array', '__return_empty_array', 10, 2);
    }

    /**
     * Checks if the discussion feature is disabled.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        static $isDisabled = null;

        if ($isDisabled !== null) {
            return $isDisabled;
        }

        $isDisabled = in_array($this->acfService->getField('disable_discussion_feature', 'option'), [true, 1, '1', 'true']);

        return $isDisabled;
    }

    /**
     * Prevents the latest comments block from rendering.
     */
    public function preventLatestCommentsBlockFromRendering(string $block_content, array $block): string
    {
        if ($block['blockName'] === 'core/latest-comments') {
            return '';
        }

        if ($block['blockName'] === 'core/group') {
            foreach ($block['innerBlocks'] as $innerBlock) {
                if ($innerBlock['blockName'] === 'core/latest-comments') {
                    return '';
                }
            }
        }

        return $block_content;
    }

    /**
     * Disables support for comments and trackbacks on all post types.
     */
    public function disableSupportForPostTypes(): void
    {
        foreach ($this->wpService->getPostTypes() as $post_type) {
            if ($this->wpService->postTypeSupports($post_type, 'comments')) {
                $this->wpService->removePostTypeSupport($post_type, 'comments');
                $this->wpService->removePostTypeSupport($post_type, 'trackbacks');
            }
        }
    }

    /**
     * Removes the recent comments meta box.
     */
    public function removeMetaBox(): void
    {
        $this->wpService->removeMetaBox('dashboard_recent_comments', 'dashboard', 'normal');
    }

    /**
     * Redirects the user if they are on the edit comments page.
     */
    public function redirectUserIfOnEditCommentsPage(): void
    {
        global $pagenow;

        if ($pagenow === 'edit-comments.php') {
            $this->wpService->wpSafeRedirect($this->wpService->adminUrl());
            exit;
        }
    }

    /**
     * Redirects the user if they are on the discussion settings page.
     */
    public function redirectUserIfOnSettingsPage(): void
    {
        global $pagenow;

        if ($pagenow === 'options-discussion.php') {
            $this->wpService->wpSafeRedirect($this->wpService->adminUrl());
            exit;
        }
    }

    /**
     * Removes the discussion settings page.
     */
    public function removeSettingsPage(): void
    {
        $this->wpService->removeSubmenuPage('options-general.php', 'options-discussion.php');
    }

    /**
     * Removes the comments link from the admin menu.
     */
    public function removeFromAdminMenu()
    {
        $this->wpService->removeMenuPage('edit-comments.php');
    }

    /**
     * Removes the comments link from the admin bar.
     *
     * @param WP_Admin_Bar $wpAdminBar
     */
    public function removeFromAdminBar(WP_Admin_Bar $wpAdminBar): void
    {
        $wpAdminBar->remove_node('comments');
    }

    /**
     * Disables the recent comments widget.
     */
    public function disableWidget(): void
    {
        $this->wpService->unregisterWidget('WP_Widget_Recent_Comments');
    }
}
