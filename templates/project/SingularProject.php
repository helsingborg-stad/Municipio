<?php

namespace Municipio\Controller;

/**
 * Class SingularProject
 * @package Municipio\Controller
 */
class SingularProject extends \Municipio\Controller\Singular implements \Municipio\Controller\Purpose
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
    
    public function setStructuredData(array $structuredData = [], string $postType = null, int $postId = null) : array
    {
        // TODO: Setup correct data for project post type
        switch ($postType) {
            case 'project':
                $description = get_the_excerpt($postId);
                $brands      = (array) get_post_meta($postId, 'brands');
                $sponsors    = (array) get_post_meta($postId, 'sponsors');
                break;
            
            default:
                $description = get_the_excerpt($postId);
                $brands      = get_post_meta($postId, 'brands', true);
                $sponsors    = get_post_meta($postId, 'sponsors', true);
                break;
        }
        
        $additionalData = [
            '@type'       => 'Project',
            'name'        => get_the_title($postId),
            'url'         => get_permalink($postId),
            'description' => $description,
        ];
        
        if (is_iterable($brands)) {
            foreach ($brands as $brand) {
                $additionalData['brand'][] = [
                    '@type' => 'organization',
                    'name' => $brand
                ];
            }
        }
        if (is_iterable($sponsors)) {
            foreach ($sponsors as $sponsor) {
                $additionalData['sponsor'][] = [
                    '@type' => 'organization',
                    'name' => $sponsor
                ];
            }
        }
        
        return array_merge($structuredData, $additionalData);
    }
}
