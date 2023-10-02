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

        add_filter('Municipio/viewData', array($this, 'appendAttachmentsData'), 11, 1);

        add_filter('Municipio/viewData', array($this, 'appendImagesData'), 12, 1);
        add_filter('Municipio/viewData', array($this, 'appendViewContactData'), 12, 1);
        add_filter('Municipio/viewData', array($this, 'appendViewQuickFactsData'), 12, 1);
        add_filter('Municipio/viewData', array($this, 'appendAboutUsData'), 12, 1);
        add_filter('Municipio/viewData', array($this, 'appendViewAccordionData'), 12, 1);
        add_filter('Municipio/viewData', array($this, 'appendViewVisitingData'), 12, 1);
        add_filter('Municipio/viewData', array($this, 'appendViewPagesData'), 12, 1);
    }

    public function appendViewData($data)
    {
        $postId = $data['post']->id;
        $metaKeys = $this->getMetaKeys();

        foreach ($metaKeys as $metaKey) {
            $snakeCaseField = $this->fromCamelCaseToSnakeCase($metaKey);
            $data['schoolData'][$metaKey] = WP::getField($snakeCaseField, $postId);
        }

        return $data;
    }

    private function fromCamelCaseToSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    private function getMetaKeys(): array
    {
        return [
            'contacts',
            'ctaApplication',
            'customExcerpt',
            'facadeImages',
            'grades',
            'images',
            'information',
            'linkFacebook',
            'linkInstagram',
            'numberOfStudents',
            'numberOfUnits',
            'openHours',
            'openHoursLeisureCenter',
            'ownChef',
            'pages',
            'profile',
            'specialization',
            'typeOfSchool',
            'videos',
            'visitingAddress',
        ];
    }

    public function appendViewContactData(array $data)
    {
        if (isset($data['schoolData']['contacts'])) {

            $contacts = $data['schoolData']['contacts'];
            $personIds = array_map(function ($contact) {
                return $contact->person;
            }, $data['schoolData']['contacts']);

            $persons = WP::getPosts(array('post_type' => 'person', 'post__in' => $personIds, 'suppress_filters' => false));
            $professionalTitleTermIds = array_map(fn($contact) => $contact->professional_title, $data['schoolData']['contacts']);
            $professionalTitleTerms = get_terms([
                'taxonomy' => 'professional_title',
                'include' => $professionalTitleTermIds,
                'hide_empty' => false
            ]);

            foreach($contacts as $contact) {
                $person = array_filter($persons, fn($person) => $person->id === $contact->person);
                $professionalTitleTerm = array_filter($professionalTitleTerms, fn($term) => $term->term_id === $contact->professional_title);

                if( $professionalTitleTerm ) {
                    $person[0]->professionalTitle = array_shift($professionalTitleTerm)->name ?? '';
                }

            }

            $data['contacts'] = !empty($persons) ? array_map(function ($person) {
                $featuredMediaID = WP::getField('featured_media', $person->id);
                $email = WP::getField('e-mail', $person->id);
                $phone = WP::getField('phone-number', $person->id);

                $featuredMedia = $featuredMediaID ? WP::getPosts([
                    'post_type' => 'school_api_attachment',
                    'post__in' => [$featuredMediaID],
                    'suppress_filters' => false
                ]) : null;

                $contact = (object)[
                    'attachment'        => !empty($featuredMedia) ? $featuredMedia[0] : null,
                    'professionalTitle' => $person->professionalTitle, // TODO: Populate with real data.
                    'email'             => $email,
                    'phone'             => $phone,
                    'name'              => $person->postTitle
                ];
                return $contact;
            }, $persons) : null;
            
        }

        if( !empty($data['contacts']) ) {
            $data['contactTitle'] = __('Contact us', 'municipio');
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

    public function appendAboutUsData($data)
    {
        $information = $data['schoolData']['information'];
        if (isset($information->about_us) && !empty($information->about_us)) {
            $data['aboutUs'] = $information->about_us;
            $data['aboutUsTitle'] = __('About the school', 'municipio');
        }

        return $data;
    }

    public function appendViewAccordionData(array $data)
    {
        $accordionData = null;
        $information = $data['schoolData']['information'];
        
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

    public function appendViewPagesData(array $data) {
        $pageIds = $data['schoolData']['pages'];

        if( !isset($pageIds) || empty($pageIds) ) {
            return $data;
        }

        $pages = WP::getPosts([
            'post_type' => 'school_page',
            'post__in' => $pageIds,
            'suppress_filters' => false
        ]);

        if (!empty($pages)) {
            $data['pages'] = array_map(function ($page) {
                return [
                    'title' => $page->postTitle,
                    'content' => $page->postExcerpt,
                    'link' => $page->permalink,
                    'linkText' => __('Read more', 'municipio')
                ];
            }, $pages);
        }

        return $data;
    }

    public function appendAttachmentsData(array $data) {
        
        $attachmentIds = array_merge(
            $data['schoolData']['facadeImages'],
            $data['schoolData']['images']
        );

        $attachments = WP::getPosts([
            'post_type' => 'school_api_attachment',
            'post__in' => $attachmentIds,
            'suppress_filters' => false
        ]);

        $attachmentsById = [];
        foreach ($attachments as $attachment) {
            $attachmentsById[$attachment->id] = $attachment;
        }

        $data['schoolData']['attachments'] = $attachmentsById;

        return $data;
    }

    public function appendImagesData($data) {

        if( !isset($data['schoolData']['attachments']) ) {
            return $data;
        }

        $data['facadeImages'] = array_filter($data['schoolData']['attachments'], function($attachmentId) use ($data) {
            return in_array($attachmentId, $data['schoolData']['facadeImages']);
        }, ARRAY_FILTER_USE_KEY);
        
        $data['images'] = array_filter($data['schoolData']['attachments'], function($attachmentId) use ($data) {
            return in_array($attachmentId, $data['schoolData']['images']);
        }, ARRAY_FILTER_USE_KEY);

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
