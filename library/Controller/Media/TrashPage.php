<?php

namespace Municipio\Controller\Media;

use Municipio\HooksRegistrar\Hookable;

use WpService\Contracts\{
    AddAction,
    AddMediaPage,
    AdminUrl,
    GetTheID,
    GetTheTitle,
    WpGetAttachmentUrl,
    WpNonceUrl,
    WpResetPostdata
};

class TrashPage implements Hookable
{
    public function __construct(public AddAction&GetTheID&WpNonceUrl&WpGetAttachmentUrl&AdminUrl&GetTheTitle&AddMediaPage&WpResetPostdata $wpService) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('admin_menu', function () {
            $this->wpService->addMediaPage(
                'Media Trash',
                'Media Trash',
                'manage_options',
                'media-trash',
                array($this, 'customMediaTrashPage')
            );
        });
    }

    public function customMediaTrashPage()
    {
        $trashedMedia = new \WP_Query([
            'post_type'      => 'attachment',
            'post_status'    => 'trash',
            'posts_per_page' => -1
        ]);

        echo '<div class="wrap"><h1>Media Trash</h1>';

        if ($trashedMedia->have_posts()) {
            echo '<div style="display:grid;grid-template-columns:repeat(auto-fill,150px);gap:20px;">';

            while ($trashedMedia->have_posts()) {
                $trashedMedia->the_post();
                $id = $this->wpService->getTheID();
                $url = $this->wpService->wpGetAttachmentUrl($id);

                $restore_url = $this->wpService->wpNonceUrl(
                    $this->wpService->adminUrl('admin-post.php?action=restore_media&attachment_id=' . $id),
                    'restore_media_' . $id
                );

                echo '<div style="text-align:center;">';
                echo '<img src="' . esc_url($url) . '" style="max-width:100%;height:auto;" alt="' . esc_attr($this->wpService->getTheTitle()) . '">';
                echo '<div style="margin-top:8px;">' . esc_html($this->wpService->getTheTitle()) . '</div>';
                echo '<a href="' . esc_url($restore_url) . '">Restore</a>';
                echo '</div>';
            }

            echo '</div>';
        } else {
            echo '<p>No trashed media.</p>';
        }

        echo '</div>';

        $this->wpService->wpResetPostdata();
    }
}
