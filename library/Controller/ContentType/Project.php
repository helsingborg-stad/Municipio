<?php

namespace Municipio\Controller\ContentType;

/**
 * Class Project
 * @package Municipio\Controller\ContentType
 */
class Project extends ContentTypeFactory implements ContentTypeComplexInterface
{

    public $secondaryContentType = [];

    public function __construct()
    {
        $this->key = 'project';
        $this->label = __('Project', 'municipio');
       
        parent::__construct($this->key, $this->label);
        
        $this->addSecondaryContentType(new Place());

    }

    public function init(): void
    {

        $this->addHooks();

    }

    public function addHooks(): void {
        
        // Append structured data for schema.org markup
        // add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 3);
    }
    /**
     * addSecondaryContentType
     *
     * @param ContentTypeComponentInterface $contentType
     * @return void
     */
    public function addSecondaryContentType(ContentTypeComponentInterface $contentType): void
    {
        $this->secondaryContentType[] = $contentType;
    }
    /**
     * Appends the structured data array (used for schema.org markup) with additional data
     *
     * @param array structuredData The structured data array that we're going to append to.
     * @param string postType The post type of the current page.
     * @param int postId The ID of the post you want to add structured data to.
     *
     * @return array The modified structured data array.
     */
    public function appendStructuredData(array $structuredData, string $postType, int $postId): array
    {
        if (empty($postId)) {
            return $structuredData;
        }

        $additionalData = [];

        if ('project' === $postType) {
            $founder     = get_the_terms($postId, 'organisation');
            $brands      = get_the_terms($postId, 'participants');
            $departments = get_the_terms($postId, 'operation');
            $sponsors    = get_the_terms($postId, 'partner');
        } else {
            $founder     = (array) get_post_meta($postId, 'founder');
            $brands      = (array) get_post_meta($postId, 'brands');
            $departments = (array) get_post_meta($postId, 'departments');
            $sponsors    = (array) get_post_meta($postId, 'sponsors');
        }

        if (is_iterable($founder) && !is_wp_error($founder)) {
            foreach ($founder as $founder) {
                $additionalData['founder'][] = [
                    '@type' => 'Organization',
                    'name'  => $founder
                ];
            }
        }
        if (is_iterable($brands) && !is_wp_error($brands)) {
            foreach ($brands as $brand) {
                $additionalData['brand'][] = [
                    '@type' => 'Organization',
                    'name'  => $brand
                ];
            }
        }
        if (is_iterable($sponsors) && !is_wp_error($sponsors)) {
            foreach ($sponsors as $sponsor) {
                $additionalData['sponsor'][] = [
                    '@type' => 'Organization',
                    'name'  => $sponsor
                ];
            }
        }
        if (is_iterable($departments) && !is_wp_error($departments)) {
            foreach ($departments as $department) {
                $additionalData['department'][] = [
                    '@type' => 'Organization',
                    'name' => $department
                ];
            }
        }
        return array_merge($structuredData, $additionalData);
    }
}
