<?php

namespace Municipio\Admin\Private;

use WpService\Contracts\AddAction;
use WpService\Contracts\DeletePostMeta;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetTerms;
use WpService\Contracts\UpdatePostMeta;

/**
 * Represents a UserGroupSelector class.
 *
 * This class is responsible for handling user group selection functionality.
 * It is located in the file UserGroupSelector.php in the directory /workspaces/municipio-deployment/wp-content/themes/municipio/library/Admin/Private/.
 */
class UserGroupSelector
{
    /**
     * Constructor for the UserGroupSelector class.
     */
    public function __construct(private AddAction&DeletePostMeta&UpdatePostMeta&GetPostMeta&GetTerms $wpService)
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
        if (empty($_POST['user-group-visibility'])) {
            $this->wpService->deletePostMeta($postId, 'user-group-visibility');
            return;
        }

        $combined = array_combine($_POST['user-group-visibility'], $_POST['user-group-visibility']);

        $this->wpService->updatePostMeta($postId, 'user-group-visibility', $combined);
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
            empty($post->post_type) ||
            empty($terms = $this->wpService->getTerms(
                [
                    'taxonomy' => 'user_group',
                    'hide_empty' => false
                ]
            ))
        ) {
            return;
        }
    
        $checked = $this->wpService->getPostMeta($post->ID, 'user-group-visibility', true) ?: [];
    
        echo '
        <div id="user-group-visibility" class="misc-pub-section" style="display: none;">
            <label>' . __('User group visibility', 'municipio') . '</label>
            <br><br>
        ';
    
        foreach ($terms as $term) {
            echo '
            <label style="display: block; margin-bottom: 5px;">
                <input type="checkbox" name="user-group-visibility[]" value="' . $term->slug . '" ' . (in_array($term->slug, $checked) ? 'checked' : '') . '>
                ' . $term->name . '
            </label>
            ';
        }
        
        echo '
        </div>
        ';
    }
}
