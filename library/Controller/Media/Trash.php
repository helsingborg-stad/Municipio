<?php

namespace Municipio\Controller\Media;

class Trash
{
    public function __construct()
    {
        // add_action('admin_menu', function () {
        //     add_media_page(
        //         'Media Trash',
        //         'Media Trash',
        //         'manage_options',
        //         'media-trash',
        //         array($this, 'customMediaTrashPage')
        //     );
        // });

        add_filter('pre_delete_attachment', array($this, 'moveMediaToTrash'), 10, 3);
        add_action('admin_post_restore_media', array($this, 'handleRestoreMedia'));
    }

    public function moveMediaToTrash($delete, $post, $forceDelete)
    {
        if ($forceDelete) {
            return $delete;
        }

        if ($post->post_status === 'trash') {
            return $delete; // Already in trash, no need to process
        }

        wp_trash_post($post->ID);
        return true;
    }

    public function handleRestoreMedia()
    {
        if (!isset($_GET['attachment_id']) || !is_numeric($_GET['attachment_id'])) {
            wp_die('Invalid attachment ID');
        }

        $attachmentId = intval($_GET['attachment_id']);

        // Verify nonce
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'restore_media_' . $attachmentId)) {
            wp_die('Nonce verification failed');
        }

        // Restore the media from trash
        $result = wp_untrash_post($attachmentId);

        if ($result) {
            wp_redirect(admin_url('upload.php?page=media-trash&restored=1'));
            exit;
        } else {
            wp_die('Failed to restore media.');
        }
    }


    // public function customMediaTrashPage()
    // {
    //     $trashedMedia = new \WP_Query([
    //         'post_type'      => 'attachment',
    //         'post_status'    => 'trash',
    //         'posts_per_page' => -1
    //     ]);

    //     echo '<div class="wrap"><h1>Media Trash</h1>';

    //     if ($trashedMedia->have_posts()) {
    //         echo '<div style="display:grid;grid-template-columns:repeat(auto-fill,150px);gap:20px;">';

    //         while ($trashedMedia->have_posts()) {
    //             $trashedMedia->the_post();
    //             $id = get_the_ID();
    //             $url = wp_get_attachment_url($id);

    //             $restore_url = wp_nonce_url(
    //                 admin_url('admin-post.php?action=restore_media&attachment_id=' . $id),
    //                 'restore_media_' . $id
    //             );

    //             echo '<div style="text-align:center;">';
    //             echo '<img src="' . esc_url($url) . '" style="max-width:100%;height:auto;" alt="' . esc_attr(get_the_title()) . '">';
    //             echo '<div style="margin-top:8px;">' . esc_html(get_the_title()) . '</div>';
    //             echo '<a href="' . esc_url($restore_url) . '">Restore</a>';
    //             echo '</div>';
    //         }

    //         echo '</div>';
    //     } else {
    //         echo '<p>No trashed media.</p>';
    //     }

    //     echo '</div>';

    //     wp_reset_postdata();
    // }
}
