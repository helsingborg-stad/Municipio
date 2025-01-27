<?php

namespace Municipio\StickyPost;

use Municipio\HooksRegistrar\Hookable;
use Municipio\StickyPost\Helper\GetStickyOption as GetStickyOptionHelper;
use WpService\Contracts\__;
use WpService\Contracts\AddAction;
use WpService\Contracts\CheckAdminReferer;
use WpService\Contracts\Checked;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostType;
use WpService\Contracts\UpdateOption;
use WpService\Contracts\UseBlockEditorForPost;
use WpService\Contracts\WpNonceField;

/**
 * Represents a AddStickyCheckboxForPost class.
 *
 * This class is responsible for adding a sticky checkbox for private posts.
 */
class AddStickyCheckboxForPost implements Hookable
{
    private const NONCE_ACTION = 'municipio_sticky_post_update';
    private const NONCE_NAME   = '_municipio_sticky_post_nonce';

    /**
     * Constructor for the AddStickyCheckboxForPost class.
     */
    public function __construct(
        private GetStickyOptionHelper $getStickyOptionHelper,
        private AddAction&CurrentUserCan&Checked&__&GetOption&UpdateOption&GetPostType&WpNonceField&CheckAdminReferer&UseBlockEditorForPost $wpService
    ) {
    }

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
    public function saveStickyCheckboxValue(int $postId): void
    {
        if ($this->wpService->useBlockEditorForPost($postId) === true) {
            return;
        }

        if (!$this->wpService->currentUserCan('edit_post', $postId)) {
            return;
        }

        if ($this->wpService->checkAdminReferer(self::NONCE_ACTION, self::NONCE_NAME) === false) {
            return;
        }

        $postType     = $this->wpService->getPostType($postId);
        $optionName   = $this->getStickyOptionHelper->getOptionKey($postType);
        $stickyOption = $this->getStickyOptionHelper->getOption($postType);

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (isset($_POST[$optionName])) {
            $stickyOption[$postId] = $postId;
        } else {
            unset($stickyOption[$postId]);
        }

        $this->wpService->updateOption($optionName, $stickyOption);
    }

    /**
     * Adds a sticky checkbox for private posts.
     *
     * This method adds a sticky checkbox for private posts. It checks if the current user has the capability to
     * edit the post. If not, the method returns without performing any action. Otherwise, it retrieves the
     * sticky post meta value for the post and determines whether the checkbox should be checked or not.
     * Finally, it renders the sticky checkbox with the provided checked value.
     *
     * @return void
     */
    public function addStickyCheckbox(): void
    {
        global $post;

        if (
            !isset($post) ||
            !$this->wpService->currentUserCan('edit_post', $post->ID) ||
            empty($post->post_type)
        ) {
            return;
        }

        $stickyOption = $this->getStickyOptionHelper->getOption($post->post_type);
        $checked      = $this->wpService->checked(array_key_exists($post->ID, $stickyOption), true, false);

        $this->renderStickyCheckbox($checked, $post->post_type);
    }

    /**
     * Renders a sticky checkbox for a private post.
     *
     * @param string $checked
     * @return void
     */
    private function renderStickyCheckbox(string $checked, string $postType): void
    {
        echo sprintf(
            '
            <div class="misc-pub-section misc-pub-sticky">
            <label><input type="checkbox" name="%s" value="true" %s> %s</label>
            ',
            $this->getStickyOptionHelper->getOptionKey($postType),
            $checked,
            $this->wpService->__('Make this post sticky', 'municipio')
        );

        $this->wpService->wpNonceField(self::NONCE_ACTION, self::NONCE_NAME, true, true);

        echo '</div>';
    }
}
