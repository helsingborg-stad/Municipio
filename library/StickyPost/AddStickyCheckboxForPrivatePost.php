<?php

namespace Municipio\StickyPost;

use Municipio\HooksRegistrar\Hookable;
use Municipio\StickyPost\Config\StickyPostConfigInterface;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddPostMeta;
use WpService\Contracts\Checked;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\DeletePostMeta;
use WpService\Contracts\GetPostMeta;

/**
 * Represents a AddStickyCheckboxForPrivatePost class.
 *
 * This class is responsible for adding a sticky checkbox for private posts.
 */
class AddStickyCheckboxForPrivatePost implements Hookable
{

    /**
     * Constructor for the AddStickyCheckboxForPrivatePost class.
     */
    public function __construct(
        private StickyPostConfigInterface $stickyPostConfig,
        private AddAction&CurrentUserCan&GetPostMeta&Checked&__&AddPostMeta&DeletePostMeta $wpService
    )
    {}

    /**
     * Adds hooks for the StickyPost class.
     *
     * This method adds hooks to display and save the sticky checkbox value for both posts and attachments.
     *
     * @return void
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('post_submitbox_misc_actions', array($this, 'addStickyCheckbox'), 10);
        $this->wpService->addAction('attachment_submitbox_misc_actions', array($this, 'addStickyCheckbox'), 10);
        $this->wpService->addAction('save_post', array($this, 'saveStickyCheckboxValue'));
        $this->wpService->addAction('edit_attachment', array($this, 'saveStickyCheckboxValue'));
    }

    /**
     * Saves the value of the sticky checkbox for a private post.
     *
     * @param int $postId The ID of the post.
     * @return void
     */
    public function saveStickyCheckboxValue($postId): void
    {
        if (!$this->wpService->currentUserCan('edit_post', $postId)) {
            return;
        }

        $metaKey = $this->stickyPostConfig->getStickyPostMetaKey();

        if (isset($_POST[$metaKey])) {
            $this->wpService->addPostMeta($postId, $metaKey, true, true);
        } else {
            $this->wpService->deletePostMeta($postId, $metaKey);
        }
    }

    /**
     * Adds a sticky checkbox for private posts.
     *
     * This method adds a sticky checkbox for private posts. It checks if the current user has the capability to edit the post. If not, the method returns without performing any action. Otherwise, it retrieves the sticky post meta value for the post and determines whether the checkbox should be checked or not. Finally, it renders the sticky checkbox with the provided checked value.
     *
     * @return void
     */
    public function addStickyCheckbox()
    {
        global $post;

        if (!$this->wpService->currentUserCan('edit_post', $post->ID)) {
            return;
        }

        $sticky = $this->wpService->getPostMeta($post->ID, $this->stickyPostConfig->getStickyPostMetaKey(), true);
        $checked = $this->wpService->checked($sticky, true, false);

        $this->renderStickyCheckbox($checked);

    }

    /**
     * Renders a sticky checkbox for a private post.
     *
     * @param bool $checked Whether the checkbox should be checked or not.
     * @return void
     */
    private function renderStickyCheckbox(bool $checked)
    {
        echo sprintf('
            <div class="misc-pub-section misc-pub-sticky">
            <label><input type="checkbox" name="sticky-post" value="true" %s> %s</label>
            </div>', 
            $checked ? 'checked' : '',
             $this->wpService->__('Make this post sticky', 'municipio')
        );
    }
}