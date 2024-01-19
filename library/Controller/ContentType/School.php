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

    public function __construct()
    {
        $this->key   = 'school';
        $this->label = __('School', 'municipio');


        parent::__construct($this->key, $this->label);

        $this->addSecondaryContentType(new Place());
        $this->addSecondaryContentType(new Person());
    }

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
                        $visitingAddresses = WP::getField('visiting_address', $postId);
                        if (!empty($visitingAddresses) && is_array($visitingAddresses)) {
                            $addresses = [];
                            foreach ($visitingAddresses as $visitingAddress) {
                                $address = new \Spatie\SchemaOrg\PostalAddress();
                                $address->streetAddress($visitingAddress['address']['name']);
                                $address->postalCode($visitingAddress['address']['post_code']);
                                $address->addressLocality($visitingAddress['address']['city']);
                                $address->addressCountry($visitingAddress['address']['country']);
                                $addresses[] = $address;
                            }
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
}
