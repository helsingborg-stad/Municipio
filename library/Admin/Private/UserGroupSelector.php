<?php

namespace Municipio\Admin\Private;

use WpService\Contracts\AddAction;
use WpService\Contracts\DeletePostMeta;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetTerms;
use WpService\Contracts\UpdatePostMeta;

class UserGroupSelector
{
    public function __construct(private AddAction&DeletePostMeta&UpdatePostMeta&GetPostMeta&GetTerms $wpService)
    {
        $this->wpService->addAction('post_submitbox_misc_actions', array($this, 'addUserVisibilitySelect'), 10);
        $this->wpService->addAction('attachment_submitbox_misc_actions', array($this, 'addUserVisibilitySelect'), 10);
        $this->wpService->addAction('save_post', array($this, 'saveUserVisibilitySelect'));
        $this->wpService->addAction('edit_attachment', array($this, 'saveUserVisibilitySelect'));
    }

    public function saveUserVisibilitySelect($postId)
    {
        if (empty($_POST['user-group-visibility'])) {
            $this->wpService->deletePostMeta($postId, 'user-group-visibility');
            return;
        }

        $combined = array_combine($_POST['user-group-visibility'], $_POST['user-group-visibility']);

        $this->wpService->updatePostMeta($postId, 'user-group-visibility', $combined);
    }

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
