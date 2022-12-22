<?php

namespace Municipio\Controller;

/**
 * Class SingularProject
 * @package Municipio\Controller
 */
class SingularProject extends \Municipio\Controller\Singular implements \Municipio\Purpose
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
    
    public function setStructuredData(array $structuredData = [], string $postType, int $postId = null) : array
    {
        switch ($postType) {
            // case project WIP
            // case 'project':
            //     $description = '<project_what>';
            //     $brands      = ['<project_organisation>'];
            //     $sponsors    = ['<project_partner>'];
            //     break;
            
            default:
                $description = get_the_excerpt($postId);
                $brands      = (array) get_post_meta($postId, 'brand');
                $sponsors    = (array) get_post_meta($postId, 'sponsor');
                break;
        }
        
        $additionalData = [
            '@type'       => 'Project',
            'name'        => get_the_title($postId),
            'url'         => get_permalink($postId),
            'description' => $description,
        ];
        
        if (!empty($brands)) {
            foreach ($brands as $brand) {
                $additionalData['brand'][] = [
                    '@type' => 'organization',
                    'name' => $brand
                ];
            }
        }
        if (!empty($sponsors)) {
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
