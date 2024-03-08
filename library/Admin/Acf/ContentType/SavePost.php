<?php

namespace Municipio\Admin\Acf\ContentType;

use Municipio\Helper\WP;

/**
 * Functionality to run on save_post action.
 */
class SavePost {

    public function addHooks():void {
        add_action('acf/save_post', [$this, 'updatePostSchemaWithAddress'], 10, 1);
    }

    /**
     * Saves the address data when a post with 'geo' field is saved.
     *
     * @param int $postId The ID of the post being saved.
     */
    public function updatePostSchemaWithAddress($postId)
    {

        if (empty($postId)) {
            return;
        }

        $postId = (int) $postId;
        $schemaData = WP::getField('schema', $postId);

        if (empty($schemaData) || empty($schemaData['geo'])) {
            return;
        }

        $schemaData['address'] = [
            'streetAddress'  => $schemaData['geo']['street_name'] . ' ' . $schemaData['geo']['street_number'],
            'postalCode'     => $schemaData['geo']['post_code'],
            'addressCountry' => $schemaData['geo']['country'],
        ];

        update_field('schema', $schemaData, $postId);
    }
}
