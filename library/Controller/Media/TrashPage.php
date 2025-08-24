<?php

namespace Municipio\Controller\Media;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\{
    AddAction,
    AddMediaPage,
    WpResetPostdata
};
use WP_Query;

class TrashPage implements Hookable
{
    public function __construct(public AddAction&AddMediaPage&WpResetPostdata $wpService) {}

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
        $lang = [
            'trashedMedia' => __('Trashed media', 'municipio'),
            'noTrashedMedia' => __('No trashed media.', 'municipio'),
            'confirmDelete' => __('Are you sure you want to permanently delete this post?', 'municipio')
        ];

        $trashedMedia = new WP_Query([
            'post_type'      => 'attachment',
            'post_status'    => 'trash',
            'posts_per_page' => -1
        ]);

        if ($trashedMedia->have_posts()) {
            $html = render_blade_view('partials.content.trash.page', [
                'posts' => $trashedMedia->posts,
                'lang' => $lang
            ]);

            echo $html;
        } else {
            echo '<p>' . $lang['noTrashedMedia'] . '</p>';
        }

        echo '</div>';

        $this->wpService->wpResetPostdata();
    }
}
