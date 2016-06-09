<?php

namespace Municipio\Admin;

class Unpublish
{
    public function __construct()
    {
        add_action('post_submitbox_misc_actions', array($this, 'setupUi'));
        add_action('save_post', array($this, 'saveUnpublish'));
        add_action('unpublish_post', array($this, 'unpublishPost'));
    }

    public function unpublishPost($postId)
    {
        wp_update_post(array(
            'ID' => (int)$postId,
            'post_status' => 'draft'
        ));

        return true;
    }

    public function saveUnpublish($postId)
    {
        $args = array(
            'post_id' => $postId
        );

        wp_unschedule_event(wp_next_scheduled('unpublish_post', $args), 'unpublish_post', $args);

        if (!isset($_POST['unpublish-active']) || $_POST['unpublish-active'] != 'true') {
            delete_post_meta($postId, 'unpublish-date');

            return;
        }

        $offset = get_option('gmt_offset');

        if ($offset > -1) {
            $offset = '-' . $offset;
        } else {
            $offset = '+' . (1 * abs($offset));
        }

        $unpublishDate = $_POST['unpublish-aa'] . '-' . $_POST['unpublish-mm'] . '-' . $_POST['unpublish-jj'] . ' ' . $_POST['unpublish-hh'] . ':' . $_POST['unpublish-mn'] . ':00';
        $unpublishTime = strtotime($offset . ' hours', strtotime($unpublishDate));

        $unpubParts = array(
            'aa' => $_POST['unpublish-aa'],
            'mm' => $_POST['unpublish-mm'],
            'jj' => $_POST['unpublish-jj'],
            'hh' => $_POST['unpublish-hh'],
            'mn' => $_POST['unpublish-mn']
        );

        update_post_meta($postId, 'unpublish-date', $unpubParts);

        wp_schedule_single_event($unpublishTime, 'unpublish_post', array(
            'post_id' => $postId
        ));
    }

    public function setupUi()
    {
        include MUNICIPIO_PATH . 'templates/admin/unpublish.php';
    }
}
