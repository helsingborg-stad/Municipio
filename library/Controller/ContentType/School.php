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
    protected function setSchemaParams(): array
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
     * Get school-related structured data for a specific post.
     *
     * @param int $postId The ID of the post.
     * @return void
     */
    public function getStructuredData(int $postId): ?array
    {
        $schemaParams = (array) $this->getSchemaParams();
        $schemaData   = (array) get_field('schema', $postId);

        if (empty($schemaParams) || empty($schemaData)) {
            return [];
        }

        $graph = new \Spatie\SchemaOrg\Graph();
        $graph->school();

        foreach ($schemaParams as $key => $param) {
            $value = $schemaData[$key] ?? null;

            if (empty($value)) {
                continue;
            }

            switch ($key) {
                case 'name':
                    $graph->school()->name($value);
                    break;
                case 'description':
                    $graph->school()->description($value);
                    break;
                case 'openingHours':
                    $graph->school()->openingHours($value);
                    break;
                case 'image':
                    $graph->school()->image($value);
                    break;
                case 'url':
                    $graph->school()->url($value);
                    break;
                case 'address':
                    $graph->school()->address($value);
                    break;
                default:
                    break;
            }
        }

        return $graph->toArray();
    }
    /**
     * Get the school address for a specific post.
     *
     * @param int $postId The ID of the post.
     * @return array The array of school addresses.
     */
    protected function getSchoolAddress(int $postId): array
    {
        $addressesData = WP::getField('PostalAddress', $postId);
        $addresses     = [];

        if (!empty($addressesData) && is_array($addressesData)) {
            foreach ($addressesData as $addressData) {
                $address = new \Spatie\SchemaOrg\PostalAddress();

                // Define valid parameters for a School address according to Schema.org
                $validParams = [
                    'streetAddress',
                    'postalCode',
                    'addressLocality',
                    'addressRegion',
                    'addressCountry'
                ];

                // Loop through each parameter and set it if it's valid for School
                foreach ($addressData as $param => $value) {
                    if (in_array($param, $validParams) && method_exists($address, $param)) {
                        $address->$param($value);
                    }
                }

                $addresses[] = $address;
            }
        }

        return $addresses;
    }
    /**
     * Handle visiting addresses and mark the method as deprecated.
     *
     * @param int $postId The ID of the post.
     * @return array An array of \Spatie\SchemaOrg\PostalAddress objects.
     */
    protected function legacyVisitingAddress(int $postId): array
    {
        _doing_it_wrong(__METHOD__, 'Using visiting_address is deprecated 
        and will be removed in future versions. Use the new address format, see https://schema.org for 
        valid parameters and naming conventions. Note that parameter keys are case sensitive.', '3.61.8');

        $visitingAddresses = WP::getField('visiting_address', $postId);
        $addresses         = [];

        if (!empty($visitingAddresses) && is_array($visitingAddresses)) {
            foreach ($visitingAddresses as $visitingAddress) {
                $address = new \Spatie\SchemaOrg\PostalAddress();
                $address->streetAddress($visitingAddress['address']['name']);
                $address->postalCode($visitingAddress['address']['post_code']);
                $address->addressLocality($visitingAddress['address']['city']);
                $address->addressCountry($visitingAddress['address']['country']);
                $addresses[] = $address;
            }
        }

        return $addresses;
    }
}
