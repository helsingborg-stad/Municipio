<?php

namespace Municipio\Upgrade\Version;

class V28 implements \Municipio\Upgrade\VersionInterface
{
    public function __construct(private \wpdb $db)
    {
        // Initialization code if needed
    }

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $args = array(
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => 'inherit',
            'post_mime_type' => 'application/font-woff'
        );

        $woffFilesQuery = new \WP_Query($args);

        if (!empty($woffFilesQuery->posts)) {
            $uploadsInstance = new \Municipio\Admin\Uploads();
            foreach ($woffFilesQuery->posts as $woffFile) {
                if (!get_post_meta($woffFile->ID, 'ttf')) {
                    $uploadsInstance->convertWOFFToTTF($woffFile->ID);
                }
            }
        }
    }
}