<?php

namespace Municipio\Admin\Private;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddPostMeta;
use WpService\Contracts\Checked;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\DeletePostMeta;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetTerms;
use WpService\Contracts\SanitizeTextField;
use WpService\Contracts\WpVerifyNonce;

/**
 * Represents a UserGroupSelector class.
 *
 * This class is responsible for handling user group selection functionality.
 * It is located in the file UserGroupSelector.php in the directory /workspaces/municipio-deployment/wp-content/themes/municipio/library/Admin/Private/.
 */
class UserGroupSelector implements Hookable
{
    private string $userGroupMetaKey  = 'user-group-visibility';
    private string $userGroupTaxonomy = 'user_group';

    /**
     * Constructor for the UserGroupSelector class.
     */
    public function __construct(private AddAction&DeletePostMeta&AddPostMeta&GetPostMeta&GetTerms&SanitizeTextField&WpVerifyNonce&CurrentUserCan&Checked $wpService)
    {
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
        $this->wpService->addAction('save_post', array($this, 'saveUserVisibilitySelect'));
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
        if (!$this->wpService->currentUserCan('edit_post', $postId)) {
            return;
        }

        $this->wpService->deletePostMeta($postId, $this->userGroupMetaKey);

        if (empty($_POST[$this->userGroupMetaKey])) {
            return;
        }

        foreach ($_POST[$this->userGroupMetaKey] as $group) {
            $group = $this->wpService->sanitizeTextField($group);
            $this->wpService->addPostMeta($postId, $this->userGroupMetaKey, $group, false);
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

        if (
            empty($terms = $this->wpService->getTerms(
                [
                    'taxonomy'   => $this->userGroupTaxonomy,
                    'hide_empty' => false
                ]
            ))
        ) {
            return;
        }

        $checked = $this->wpService->getPostMeta($post->ID, $this->userGroupMetaKey) ?: [];
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
            $this->userGroupMetaKey,
            __('User group visibility', 'municipio')
        );

        foreach ($terms as $term) {
            echo sprintf(
                '<label style="display: block; margin-bottom: 5px;">
                <input type="checkbox" name="%s[]" value="%s" %s>
                %s
            </label>',
                $this->userGroupMetaKey,
                $term->slug,
                $this->wpService->checked(in_array($term->slug, $checked), true, false),
                $term->name
            );
        }

        echo '</div>';
    }
}
