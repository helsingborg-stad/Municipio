<?php

namespace Intranet\Admin;

class Users
{
    public function __construct()
    {
        add_action('restrict_manage_users', array($this, 'tablenavButtons'));
        add_action('wp_ajax_sync_network_users', array($this, 'syncNetworkUsers'));
    }

    public function tablenavButtons($which)
    {
        if (!is_user_admin() && is_main_site() || $which != 'top') {
            return;
        }

        echo '
            <div class="users-sync-with-network" style="display:inline-block; vertical-align: bottom; margin-left: 20px;">
                <button type="button" class="button button-primary" data-action="users-sync-with-network" data-blogid="', get_current_blog_id() ,'">', __('Sync with network (cron)', 'municipio-intranet'), '</button>
                <span class="spinner"></span>
            </div>
        ';
    }

    public function syncNetworkUsers()
    {
        if (!defined('DOING_AJAX') || !DOING_AJAX) {
            return;
        }

        if (!isset($_POST['blog_id']) || empty($_POST['blog_id']) || !is_numeric($_POST['blog_id'])) {
            wp_die();
        }

        if (function_exists('count_users') && count_users()['total_users'] > 100) {
            wp_schedule_single_event(time() + 5, 'wpmu_new_blog_cron', array($blogId));
            wp_send_json(array('success' => 'cron'));
        } else {
            $registration = new \Intranet\User\Registration();
            $registration->addUsersToNewBlogCron($blogId);
            wp_send_json(array('success' => true));
        }

        wp_die();
    }
}
