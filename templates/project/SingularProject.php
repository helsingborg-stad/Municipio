<?php

namespace Municipio\Controller;

use Municipio\Helper\Purpose;
use Municipio\Helper\Data;

/**
 * Class SingularProject
 * @package Municipio\Controller
 */
class SingularProject extends \Municipio\Controller\Singular implements \Municipio\Controller\Purpose, \Municipio\Controller\SingularPurpose
{
    public function __construct()
    {
        parent::__construct();

        // // Example:
        // $challenges = wp_get_post_terms(get_queried_object_id(), 'challenge_category');
        // if (!empty($challenges)) {
        //     $this->data['content']['sidebar.right-sidebar.before'] = [
        //         'elementType'   => 'aside',
        //         'content'       => print_r($challenges, true),
        //     ];
        //     // Prepare for view
        //     Purpose::prepareViewData($this->data);
        // }

        //Setup schema.org data
        add_filter('Municipio/StructuredData', array($this, 'setStructuredData'), 10, 3);
        $this->data['structuredData'] = Data::getStructuredData(
            $this->data['postType'],
            $this->getPageID()
        );
    }

    /**
     * The localized, singular label used to describe this class in dropdowns and similar circumstances.
     *
     * @return string The label
     */
    public static function getLabel(): string
    {
        return __('Project', 'municipio');
    }
    /**
     * Returns the last part of the class name, in lowercase.
     *
     * @return string The name of the class without the namespace and without the word "Singular"
     */
    public static function getKey(): string
    {
        return strtolower(last(explode('\\', str_replace('Singular', '', get_class()))));
    }
    /**
     * Returns the the parent class name, in lowercase.
     *
     * @return string The name of the parent class without the namespace
     */
    public static function getType(): string
    {
        return strtolower(last(explode('\\', get_parent_class())));
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
