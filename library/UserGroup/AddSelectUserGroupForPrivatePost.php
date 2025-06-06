<?php

namespace Municipio\UserGroup;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddPostMeta;
use WpService\Contracts\Checked;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\DeletePostMeta;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetTerms;
use WpService\Contracts\SanitizeTextField;
use Municipio\Admin\Private\Config\UserGroupRestrictionConfig;
use Municipio\Helper\User\Config\UserConfig as UserHelperConfig;
use Municipio\Helper\User\GetUserGroupTerms;
use WpService\Contracts\UseBlockEditorForPost;
use WpService\Contracts\WpNonceField;
use WpService\Contracts\WpVerifyNonce;

/**
 * Represents a UserGroupSelector class.
 *
 * This class is responsible for handling user group selection functionality.
 */
class AddSelectUserGroupForPrivatePost implements Hookable
{
    private const NONCE_ACTION = 'municipio_user_group_visibility_update';
    private const NONCE_NAME   = '_municipio_user_group_visibility_nonce';

    /**
     * Constructor for the UserGroupSelector class.
     */
    public function __construct(
        private AddAction&DeletePostMeta&AddPostMeta&GetPostMeta&GetTerms&SanitizeTextField&CurrentUserCan&Checked&WpNonceField&WpVerifyNonce&UseBlockEditorForPost $wpService,
        private string $userGroupTaxonomyName,
        private UserHelperConfig $userHelperConfig,
        private UserGroupRestrictionConfig $userGroupRestrictionConfig,
        private GetUserGroupTerms $getUserGroupTerms
    ) {
    }

    /**
     * Adds hooks for the UserGroupSelector class.
     *
     * This method adds hooks for the UserGroupSelector class.
     *
     * @return void
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('post_submitbox_misc_actions', array($this, 'addUserVisibilitySelect'), 10);
        $this->wpService->addAction('attachment_submitbox_misc_actions', array($this, 'addUserVisibilitySelect'), 10);
        $this->wpService->addAction('edit_post', array($this, 'saveUserVisibilitySelect'));
        $this->wpService->addAction('edit_attachment', array($this, 'saveUserVisibilitySelect'));
    }

    /**
     * Handles saving user group visibility settings for a post.
     *
     * This function checks if the 'user-group-visibility' input is present in the `$_POST` data.
     * If it is empty, the corresponding post meta is deleted. Otherwise, it combines the input
     * into an associative array and updates the 'user-group-visibility' post meta.
     *
     * @param int $postId The ID of the post being saved.
     *
     * @return void
     */
    public function saveUserVisibilitySelect($postId)
    {
        if ($this->wpService->useBlockEditorForPost($postId) === true) {
            return;
        }

        if (!$this->wpService->currentUserCan('edit_post', $postId)) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (empty($_POST[self::NONCE_NAME]) || !$this->wpService->wpVerifyNonce($_POST[self::NONCE_NAME], self::NONCE_ACTION)) {
            return;
        }

        $metaKey = $this->userGroupRestrictionConfig->getUserGroupVisibilityMetaKey();

        $this->wpService->deletePostMeta($postId, $metaKey);

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (empty($_POST[$metaKey])) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        foreach ($_POST[$metaKey] as $group) {
            $this->wpService->addPostMeta(
                $postId,
                $metaKey,
                $this->wpService->sanitizeTextField($group),
                false
            );
        }
    }

    /**
     * Adds a user visibility select field to the admin panel.
     *
     * This method adds a user visibility select field to the admin panel for a specific post.
     * The select field allows the user to choose which user groups can see the post.
     * The user groups are retrieved from the 'user_group' taxonomy.
     * The select field is populated with checkboxes for each user group.
     * The checkboxes are pre-checked if the post has already been assigned to those user groups.
     *
     * @return void
     */
    public function addUserVisibilitySelect()
    {
        global $post;

        $terms = $this->getUserGroupTerms->get();

        if (empty($terms) || !is_array($terms)) {
            return;
        }

        $checked = $this->wpService->getPostMeta(
            $post->ID,
            $this->userGroupRestrictionConfig->getUserGroupVisibilityMetaKey()
        ) ?: [];
        $this->renderPrivateVisibilityList($terms, $checked);
    }

    /**
     * Renders the private visibility list.
     *
     * This method is responsible for rendering the private visibility list in the user group selector.
     * It generates HTML code for displaying checkboxes with user group terms.
     *
     * @param array $terms   An array of user group terms.
     * @param array $checked An array of checked user group terms.
     *
     * @return void
     */
    private function renderPrivateVisibilityList(array $terms, array $checked)
    {
        echo sprintf(
            '<div id="%s" class="misc-pub-section" style="display: none;">
            <label>%s</label>
            <br><br>',
            $this->userGroupRestrictionConfig->getUserGroupVisibilityMetaKey(),
            __('User group visibility', 'municipio')
        );

        foreach ($terms as $term) {
            echo sprintf(
                '<label style="display: block; margin-bottom: 5px;">
                <input type="checkbox" name="%s[]" value="%s" %s>
                %s
            </label>',
                $this->userGroupRestrictionConfig->getUserGroupVisibilityMetaKey(),
                $term->slug,
                $this->wpService->checked(in_array($term->slug, $checked), true, false),
                $term->name
            );
        }

        $this->wpService->wpNonceField(self::NONCE_ACTION, self::NONCE_NAME, true, true);

        echo '</div>';
    }
}
