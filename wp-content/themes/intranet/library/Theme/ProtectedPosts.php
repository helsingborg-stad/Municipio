<?php

namespace Intranet\Theme;

class ProtectedPosts
{
    public function __construct()
    {
        add_action('init', array($this, 'addProtectedPostStatus'));
        add_action('post_submitbox_misc_actions', array($this, 'protectedStatusField'));
        add_action('save_post', array($this, 'saveProtectedPostStatus'));
    }

    public function saveProtectedPostStatus($postId)
    {
        if (!isset($_POST['visibility']) || $_POST['visibility'] != 'protected') {
            return;
        }

        remove_action('save_post', array($this, 'saveProtectedPostStatus'));

        wp_update_post(array(
            'ID' => $postId,
            'post_status' => 'protected'
        ));

        add_action('save_post', array($this, 'saveProtectedPostStatus'));

        return true;
    }

    public function protectedStatusField($post)
    {
        $checked = checked('protected', $post->post_status, false);
        echo '
            <div class="misc-pub-section intranet-protected">
                <label><input type="checkbox" name="visibility" value="protected" ' . $checked . '> ' . __('Require login to view', 'municipio-intranet') . '</label>
            </div>
        ';
    }

    public function addProtectedPostStatus()
    {
        register_post_status('protected', array(
            'label' => 'Protected',
            'public' => is_user_logged_in(),
            'internal' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>'),
        ));
    }
}
