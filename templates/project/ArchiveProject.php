<?php

namespace Municipio\Controller;

/**
 * Class ArchiveProject
 *
 * @package Municipio\Controller
 */
class ArchiveProject extends \Municipio\Controller\Archive implements \Municipio\Purpose
{
    public function __construct()
    {
        parent::__construct();
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
         return [];
     }
}
