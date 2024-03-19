<?php

namespace Municipio\Admin\Acf\ContentType;

use Municipio\Helper\ContentType;
use Municipio\Helper\WP;

/**
 * Functionality to run on save_post action.
 */
class SavePost {

    public function addHooks():void {
        add_action('acf/save_post', [$this, 'updatePostSchemaWithAddress'], 10, 1);

        add_action('acf/save_post', [$this, 'populateEmptySchemaFields'], 10, 1);
    }

    /**
     * Populates empty schema fields with data from the post.
     *
     * @param int $postId The ID of the post being saved.
     * @return void
     */
    public function populateEmptySchemaFields($postId): void {

        if (empty($postId)) {
            return;
        }

        $postId = (int) $postId;
        $postType = get_post_type($postId);
        $contentType = ContentType::getContentType($postType);

        if(!$contentType) {
            return;
        }

        $schemaParams = $contentType->getSchemaParams();
        if(empty($schemaParams)) {
            return;
        }

        $schemaData = WP::getField('schema', $postId);

        if(!empty($schemaParams['name']) && empty($schemaData['name'])) {
            $schemaData['name'] = get_the_title($postId);
            update_field('schema', $schemaData, $postId);
        }
        if(!empty($schemaParams['description']) && empty($schemaData['description'])) {
            $schemaData['description'] = get_the_excerpt($postId);
            update_field('schema', $schemaData, $postId);
        }

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
