<?php

namespace Municipio\Upgrade\Version;

use WpService\WpService;

class V33 implements \Municipio\Upgrade\VersionInterface
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
        $destinationValues = [];
        foreach (get_post_types() as $postType) {
            $schemaType = get_field('schema', $postType . '_options');

            if (empty($schemaType)) {
                continue;
            }

            $destinationValues[] = [
                'post_type'   => $postType,
                'schema_type' => $schemaType
            ];
        }

        if (!empty($destinationValues)) {
            update_field('post_type_schema_types', $destinationValues, 'option');
        }

    }
}