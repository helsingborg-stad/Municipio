<?php

namespace Municipio\Controller;

/**
 * Class SingularPurpose
 * @package Municipio\Controller
 */
class SingularPurpose extends \Municipio\Controller\Singular
{
    public function __construct()
    {
        parent::__construct();

        add_filter('Municipio/StructuredData', array($this, 'setStructuredData'), 10, 3);

        $this->data['structuredData'] = \Municipio\Helper\Data::getStructuredData(
            $this->data['postType'],
            $this->getPageID()
        );
    }
    /**
     * It takes the structured data array, adds a new key/value pair to it, and returns the new array
     *
     * @param structuredData The structured data that's already been set.
     * @param postType The post type of the post you're adding structured data to.
     * @param postId The ID of the post you're adding structured data to.
     *
     * @return An array with the structured data for the project post type.
     */
    public function termsToStructuredData(string $taxonomy, int $postId, string $type, string $schemaType)
    {
        $terms = wp_get_post_terms($postId, $taxonomy, 'names');
        if (is_iterable($terms)) {
            $additionalData = [];
            foreach ($terms as $term) {
                $additionalData[$type][] = [
                    '@type' => $schemaType,
                    'name'  => $term
                ];
            }
            return $additionalData;
        }

        return false;
    }
    public function setStructuredData(array $structuredData = [], string $postType = null, int $postId = null): array
    {
        switch ($postType) {
            case 'project': // Post type, not purpose!
                $description = get_post_meta($postId, 'project_what', true);
                $founder     = wp_get_post_terms($postId, 'organisation', 'names');
                $brands      = wp_get_post_terms($postId, 'participants', 'names');
                $department  = wp_get_post_terms($postId, 'operation', 'names');
                $sponsors    = wp_get_post_terms($postId, 'partner', 'names');
                $location    = (array) get_post_meta($postId, 'map', true);

                break;

            default:
                $description = get_the_excerpt($postId);
                $founder     = false;
                $brands      = false;
                $department  = false;
                $sponsors    = false;
                $location    = false;
                break;
        }

        $additionalData = [
            '@type'       => 'Project',
            'name'        => get_the_title($postId),
            'url'         => get_permalink($postId),
            'description' => $description,
        ];

        if (is_iterable($founder)) {
            foreach ($founder as $founder) {
                $additionalData['founder'][] = [
                    '@type' => 'Organization',
                    'name'  => $founder
                ];
            }
        }
        if (is_iterable($brands)) {
            foreach ($brands as $brand) {
                $additionalData['brand'][] = [
                    '@type' => 'Organization',
                    'name'  => $brand
                ];
            }
        }
        if (is_iterable($sponsors)) {
            foreach ($sponsors as $sponsor) {
                $additionalData['sponsor'][] = [
                    '@type' => 'Organization',
                    'name'  => $sponsor
                ];
            }
        }
        if ($department) {
            $additionalData['department'][] = [
                '@type' => 'Organization',
                'name' => $department
            ];
        }
        if (!empty($location['address'])) {
            $additionalData['location'][] = [
                '@type'   => 'Place',
                'address' => $location['address'],
            ];
        }
        if (!empty($location['lat']) && !empty($location['lng'])) {
            $additionalData['location'][] = [
                '@type'     => 'GeoCoordinates',
                'latitude'  => $location['lat'],
                'longitude' => $location['lng'],
            ];
        }
        if (!empty($location['country'])) {
            $additionalData['location'][] = [
                '@type'     => 'PostalAddess',
                'country'  => $location['country'],
            ];
        }

        return array_merge($structuredData, $additionalData);
    }
}
