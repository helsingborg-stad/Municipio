<?php

namespace Intranet\Admin;

class RemoveSite
{
    public function __construct()
    {
        add_action('delete_blog', array($this, 'removeFollowers'));
        add_action('deactivate_blog', array($this, 'removeFollowers'));
        add_action('archive_blog', array($this, 'removeFollowers'));
        add_action('make_spam_blog', array($this, 'removeFollowers'));
    }

    public function removeFollowers($blogId)
    {
        global $wpdb;

        $query = "
            UPDATE {$wpdb->usermeta}
            SET
                meta_value = REPLACE(meta_value, '\",\"{$blogId}\"]', '\"]'),
                meta_value = REPLACE(meta_value, '\"{$blogId}\",\"', '\"'),
                meta_value = REPLACE(meta_value, '\"{$blogId}\"', '')
            WHERE
                INSTR(meta_value, '\"{$blogId}\"') > 0
                AND meta_key = 'intranet_subscriptions'
        ";

        $res = $wpdb->query($query);
    }
}
