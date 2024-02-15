<?php

namespace Municipio\Controller\ContentType;

use Municipio\Controller\ContentType\School\SchoolDataPreparer;
use Municipio\Helper\WP;

/**
 * Class School
 * @package Municipio\Controller\ContentType
 */
class School extends ContentTypeFactory implements ContentTypeComplexInterface
{
    protected object $postMeta;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->key   = 'school';
        $this->label = __('School', 'municipio');

        $this->addSecondaryContentType(new Place());
        $this->addSecondaryContentType(new Person());

        $this->schemaParams = $this->applySchemaParamsFilter();

        parent::__construct($this->key, $this->label);
    }

    /**
     * Add hooks for the School content type.
     *
     * @return void
     */
    public function addHooks(): void
    {
        $dataPreparer = new SchoolDataPreparer();

        add_filter('Municipio/viewData', [$dataPreparer, 'prepareData'], 10, 1);
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
     * Set the schema parameters for the School content type.
     *
     * @return array The array of schema parameters.
     */
    protected function schemaParams(): array
    {
        $params = [
            'name'         => [
                'schemaType' => 'Text',
                'label'      => __('Name', 'municipio')
            ],
            'description'  => [
                'schemaType' => 'Text',
                'label'      => __('Description', 'municipio')
            ],
            'url'          => [
                'schemaType' => 'URL',
                'label'      => __('URL', 'municipio')
            ],
            'email'        => [
                'schemaType' => 'Text',
                'label'      => __('Email', 'municipio')
            ],
            'telephone'    => [
                'schemaType' => 'Text',
                'label'      => __('Phone', 'municipio')
            ],
            'image'        => [
                'schemaType' => 'ImageObject',
                'label'      => __('Image', 'municipio')
            ],
            'openingHours' => [
                'schemaType' => 'Text',
                'label'      => __('Opening hours', 'municipio')
            ],
        ];
        foreach ($this->getSecondaryContentType() as $contentType) {
            switch ($contentType->getKey()) {
                case 'place':
                    $placeParams       = $contentType->getSchemaParams();
                    $params['address'] = $placeParams['geo'];
                    break;

                default:
                    break;
            }
        }

        return $params;
    }

    /**
     * Get the schema entity for the School content type.
     *
     * @param \Spatie\SchemaOrg\Graph $graph The schema graph.
     * @return mixed The schema entity.
     */
    protected function getSchemaEntity(\Spatie\SchemaOrg\Graph $graph)
    {
        return $graph->school(); // Return the specific schema entity for School
    }

    /**
     * @param int $postId The ID of the post.
     * @return array The array of structured data.
     */
    protected function legacyGetStructuredData(int $postId): array
    {

        $structuredData = [
            '@type'       => 'School',
            'name'        => get_the_title($postId),
            'description' => get_the_excerpt($postId),
        ];

        $meta = [
            'open_hours',
            'open_hours_leisure_center'
        ];

        foreach ($meta as $key) {
            $value = WP::getField($key, $postId);
            if (!empty($value)) {
                $structuredData[$key] = $value;
            }
        }

        $structuredData['address'] = $this->legacyVisitingAddress($postId);
        return $structuredData;
    }

    /**
    //  * Handle visiting addresses and mark the method as deprecated.
    //  *
    //  * @param int $postId The ID of the post.
    //  * @return array An array of \Spatie\SchemaOrg\PostalAddress objects.
    //  */
    protected function legacyVisitingAddress(int $postId): array
    {
        _doing_it_wrong(__METHOD__, 'Using visiting_address is deprecated
        and will be removed in future versions. Use the new address format, see https://schema.org for
        valid parameters and naming conventions. Note that parameter keys are case sensitive.', '3.61.8');

        $visitingAddresses = WP::getField('visiting_address', $postId);
        $addresses         = [];

        if (!empty($visitingAddresses) && is_array($visitingAddresses)) {
            foreach ($visitingAddresses as $visitingAddress) {
                $visitingAddress = (array) $visitingAddress->address;

                $address = new \Spatie\SchemaOrg\PostalAddress();
                $address->streetAddress($visitingAddress['address']);
                $address->postalCode($visitingAddress['post_code']);
                $address->addressLocality($visitingAddress['city']);
                $address->addressCountry($visitingAddress['country']);
                $addresses[] = $address;
            }
        }

        return $addresses;
    }
}
