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
    public $secondaryContentType = [];
    protected object $postMeta;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->key   = 'school';
        $this->label = __('School', 'municipio');

        parent::__construct($this->key, $this->label);

        $this->addSecondaryContentType(new Place());
        $this->addSecondaryContentType(new Person());
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
     * Get the secondary content types associated with the school.
     *
     * @return array The array of secondary content types.
     */
    public function getSecondaryContentType(): array
    {
        return $this->secondaryContentType;
    }

    /**
     * Get school-related structured data for a specific post.
     *
     * @param int $postId The ID of the post.
     * @return void
     */
    public function getStructuredData(int $postId): ?array
    {
        $graph = new \Spatie\SchemaOrg\Graph();

        $graph->school()
        ->name(get_the_title($postId))
        ->description(get_the_excerpt($postId))
        ->image(get_the_post_thumbnail_url($postId))
        ->url(get_permalink($postId))
        ->numberOfStudents(WP::getField('numberOfStudents', $postId))
        ->openingHours(WP::getField('openingHours', $postId));

        if (!empty($this->getSecondaryContentType())) {
            foreach ($this->getSecondaryContentType() as $contentType) {
                switch ($contentType->getKey()) {
                    case 'place':
                        $addresses = $this->getSchoolAddress($postId);
                        // If no addresses found, use legacy method
                        if (empty($addresses)) {
                            $addresses = $this->legacyVisitingAddress($postId);
                        }
                        if (!empty($addresses)) {
                            $graph->school()->address($addresses);
                            $graph->hide(\Spatie\SchemaOrg\PostalAddress::class);
                        }
                        break;

                    case 'person':
                        $contacts = WP::getField('contacts', $postId);
                        if (!empty($contacts) && is_array($contacts)) {
                            $contactPoints = [];
                            foreach ($contacts as $contact) {
                                if (empty($contact['person']) || !isset($contact['person']->ID)) {
                                    continue;
                                }
                                $contactPoint = new \Spatie\SchemaOrg\ContactPoint();

                                $contactPoint->contactType($contact['professional_title']);

                                $phone = WP::getField('phone-number', $contact['person']->ID);
                                $contactPoint->telephone($phone);

                                $email = WP::getField('e-mail', $contact['person']->ID);
                                $contactPoint->email($email);

                                $contactPoints[] = $contactPoint;
                            }
                            $graph->school()->contactPoint($contactPoints);
                        }
                        break;
                }
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
