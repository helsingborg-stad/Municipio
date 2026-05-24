<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

class V29 implements \Municipio\Upgrade\VersionInterface
{
    public function __construct(private \wpdb $db, private WpService $wpService)
    {
        // Initialization code if needed
    }

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $args = [
            'posts_per_page' => -1,
            'meta_key'       => 'location',
            'post_type'      => 'any',
            'post_status'    => 'publish'
        ];

        $posts = get_posts($args);
        if (!empty($posts) && is_array($posts)) {
            foreach ($posts as $post) {
                $schemaField = get_field('schema', $post->ID) ?? [];
                if (is_array($schemaField)) {
                    $locationField      = get_post_meta($post->ID, 'location', true);
                    $schemaField['geo'] = !empty($schemaField['geo']) ? $schemaField['geo'] : $locationField;

                    update_field('schema', $schemaField, $post->ID);
                }
            }
        }
    }
}