<?php

namespace Municipio\Controller\ContentType;

use Municipio\Helper\ContentType as Helper;
use Municipio\Helper\WP;

/**
 * Class School
 * @package Municipio\Controller\ContentType
 */
class School extends ContentTypeFactory implements ContentTypeComplexInterface
{

    protected $secondaryContentType = [];
    
    public function __construct()
    {
        $this->key = 'school';
        $this->label = __('School', 'municipio');

        parent::__construct($this->key, $this->label);

        $this->addSecondaryContentType(new Place());
        $this->addSecondaryContentType(new Person());
    }

    public function addHooks(): void {
        // Append structured data for schema.org markup
        add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 3);

        add_filter('Municipio/viewData', array($this, 'appendViewData'), 10, 1);
        add_filter('Municipio/viewData', array($this, 'appendViewContactData'), 11, 1);
        add_filter('Municipio/viewData', array($this, 'appendViewQuickFactsData'), 11, 1);
        add_filter('Municipio/viewData', array($this, 'appendViewAccordionData'), 11, 1);
        add_filter('Municipio/viewData', array($this, 'appendViewVisitingData'), 11, 1);
    }

    public function appendViewData($data) {
        $postId = $data['post']->id;

        $data['schoolData']['pages'] = WP::getField('pages', $postId, false);
        $data['schoolData']['typeOfSchool'] = WP::getField('type_of_school', $postId);
        $data['schoolData']['facadeImages'] = WP::getField('facade_images', $postId);
        $data['schoolData']['images'] = WP::getField('images', $postId);
        $data['schoolData']['videos'] = WP::getField('videos', $postId);
        $data['schoolData']['numberOfStudents'] = WP::getField('number_of_students', $postId);
        $data['schoolData']['numberOfUnits'] = WP::getField('number_of_units', $postId);
        $data['schoolData']['grades'] = WP::getField('grades', $postId);
        $data['schoolData']['openHours'] = WP::getField('open_hours', $postId);
        $data['schoolData']['openHoursLeisureCenter'] = WP::getField('open_hours_leisure_center', $postId);
        $data['schoolData']['specialization'] = WP::getField('specialization', $postId);
        $data['schoolData']['profile'] = WP::getField('profile', $postId);
        $data['schoolData']['ownChef'] = WP::getField('own_chef', $postId);
        $data['schoolData']['linkInstagram'] = WP::getField('link_instagram', $postId);
        $data['schoolData']['linkFacebook'] = WP::getField('link_facebook', $postId);
        $data['schoolData']['customExcerpt'] = WP::getField('custom_excerpt', $postId);
        $data['schoolData']['information'] = WP::getField('information', $postId);
        $data['schoolData']['visitingAddress'] = WP::getField('visiting_address', $postId);
        $data['schoolData']['contacts'] = WP::getField('contacts', $postId);
        $data['schoolData']['ctaApplication'] = WP::getField('cta_application', $postId);

        return $data;
    }

    public function appendViewContactData(array $data)
    {
        if (isset($data['schoolData']['contacts'])) {
            
            $personIds = array_map(function ($contact) {
                return $contact->person;
            }, $data['schoolData']['contacts']);

            $persons = WP::getPosts(array('post_type' => 'person', 'post__in' => $personIds, 'suppress_filters' => false));

            $data['contacts'] = !empty($persons) ? array_map(function ($person) {
                $email = WP::getField('e-mail', $person->id);
                $phone = WP::getField('phone-number', $person->id);
                $contact = (object)[
                    'professionalTitle' => 'Rektor', // TODO: Populate with real data.
                    'email'             => $email,
                    'phone'             => $phone,
                    'name'              => $person->postTitle
                ];
                return $contact;
            }, $persons) : null;
            
        }

        return $data;
    }

    public function appendViewQuickFactsData(array $data)
    {

        $quickFacts = [];
        $schoolData = $data['schoolData'];

        if (isset($schoolData['ownChef']) && $schoolData['ownChef'] === true) {
            $quickFacts[] = ['label' => __('Own chef', 'municipio')];
        }

        if (!empty($schoolData['numberOfStudents'])) {
            $value = absint($schoolData['numberOfStudents']);
            $label = sprintf(__('Number of students: %d', 'municipio'), $value);
            $quickFacts[] = ['label' => $label];
        }

        if (!empty($quickFacts)) {
            $data['quickFactsTitle'] = __('Facts', 'municipio');
            $data['quickFacts'] = $quickFacts;
        }

        return $data;
    }

    public function appendViewAccordionData(array $data)
    {
        $accordionData = null;
        $information = $data['schoolData']['information'];

        if(isset($information->about_us) && !empty($information->about_us)) {
            $accordionData[] = [
                'heading' => __('About the school', 'municipio'),
                'content' => $information->about_us
            ];
        }
        
        if(isset($information->how_we_work) && !empty($information->how_we_work)) {
            $accordionData[] = [
                'heading' => __('How we work', 'municipio'),
                'content' => $information->how_we_work
            ];
        }
        
        if(isset($information->optional) && !empty($information->optional)) {
            foreach($information->optional as $optional) {
                $accordionData[] = [
                    'heading' => $optional->heading ?? '',
                    'content' => $optional->content ?? ''
                ];
            }
        }

        $data['accordionData'] = $accordionData;
        return $data;
    }

    public function appendViewVisitingData(array $data) {
        $visitingAddresses = $data['schoolData']['visitingAddress'];
        $visitingData = [];
        $mapPins = [];

        if( !isset($visitingAddresses) || empty($visitingAddresses) ) {
            return $data;
        }

        foreach( $visitingAddresses as $visitingAddress ) {
            $visitingData[] = $visitingAddress->address;
            $mapPins[] = [
                'lat' => $visitingAddress->address->lat,
                'lng' => $visitingAddress->address->lng,
                'tooltip' => ['title' => $visitingAddress->address->address]
            ];
        }

        if(isset($visitingAddresses->address) && !empty($visitingAddresses->address)) {
            $visitingData[] = [
                'heading' => __('Visiting address', 'municipio'),
                'content' => $visitingAddress->address
            ];
        }

        if( !empty($visitingData) ) {;
            
            $lats = array_map(fn($pin) => $pin['lat'], $mapPins);
            $lngs = array_map(fn($pin) => $pin['lng'], $mapPins);
            $maxLat = max($lats);
            $minLat = min($lats);
            $maxLng = max($lngs);
            $minLng = min($lngs);
            
            $startLat = $minLat + (($maxLat - $minLat)/2);
            $startLng = $minLng + (($maxLng - $minLng)/2);

            $data['visitingAddresses'] = $visitingData;
            $data['visitingAddressMapPins'] = $mapPins;
            $data['visitingAddressMapStartPosition'] = ['lat' => $startLat, 'lng' => $startLng, 'zoom' => 13];
            $data['visitingDataTitle'] = sizeof($visitingData) === 1
                ? __('Visiting address', 'municipio')
                : __('Visiting addresses', 'municipio');
        }
        
        
        return $data;
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

        $additionalData = [
            '@context' => 'https://schema.org',
            '@type' => 'School',
        ];

        $properties = Helper::getStructuredDataProperties([
            'name',
            'description', // TODO Define which meta to use for this. Use the filter hook declared in Helper for this.
            'numberOfStudents',
            'openingHours',
            'slogan' // TODO Define which meta to use for this. Use the filter hook declared in Helper for this.
        ], $postId);

        return Helper::appendStructuredData($properties, $postId, $structuredData, $additionalData);
    }
}
