<?php

namespace Municipio\Controller\ContentType\School;

use Municipio\Helper\Controller;
use Municipio\Helper\WP;

class SchoolDataPreparer implements DataPrepearerInterface
{

    private object $postMeta;
    private array $data;

    public function prepareData(array $data): array
    {
        $this->data = $data;
        $metaKeys = $this->getMetaKeys();
        $this->postMeta = new \stdClass();

        foreach ($metaKeys as $metaKey) {
            $fieldName = $this->getFieldName($metaKey);
            $this->postMeta->{$fieldName} = $this->getFieldValue($metaKey);
        }

        $this->appendAttachmentsData();
        $this->appendImagesData();
        $this->appendViewContactData();
        $this->appendViewQuickFactsData();
        $this->appendViewAccordionData();
        $this->appendViewVisitingData();
        $this->appendViewPagesData();
        $this->appendSocialMediaLinksData();

        return $this->data;
    }

    private function getFieldName(string $fieldName): string
    {
        return lcfirst(Controller::camelCase($fieldName));
    }

    private function getFieldValue($fieldName)
    {
        return WP::getField($fieldName, $this->data['post']->id);
    }

    private function getMetaKeys(): array
    {
        return [
            'area',
            'contacts',
            'cta_application',
            'custom_excerpt',
            'facade_images',
            'grades',
            'images',
            'information',
            'link_facebook',
            'link_instagram',
            'number_of_students',
            'number_of_units',
            'open_hours',
            'open_hours_leisure_center',
            'own_chef',
            'pages',
            'profile',
            'specialization',
            'type_of_school',
            'videos',
            'visiting_address',
        ];
    }

    private function appendViewContactData(): void
    {
        if (!isset($this->postMeta->contacts)) {
            return;
        }

        $personIds = array_map(function ($contact) {
            return $contact->person;
        }, $this->postMeta->contacts);

        $persons = WP::getPosts(array('post_type' => 'person', 'post__in' => $personIds, 'suppress_filters' => false));
        $professionalTitleTermIds = array_map(fn ($contact) => $contact->professional_title, $this->postMeta->contacts);
        $professionalTitleTerms = get_terms([
            'taxonomy' => 'professional_title',
            'include' => $professionalTitleTermIds,
            'hide_empty' => false
        ]);

        foreach ($this->postMeta->contacts as $contact) {
            $person = array_filter($persons, fn ($person) => $person->id === $contact->person);

            if (is_wp_error($professionalTitleTerms)) {
                continue;
            }

            $professionalTitleTerm = array_filter($professionalTitleTerms, fn ($term) => $term->term_id === $contact->professional_title);

            if ($professionalTitleTerm) {
                reset($person)->professionalTitle = array_shift($professionalTitleTerm)->name ?? '';
            }
        }

        $this->data['contacts'] = !empty($persons) ? array_map(function ($person) {
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
                'professionalTitle' => $person->professionalTitle,
                'email'             => $email,
                'phone'             => $phone,
                'name'              => $person->postTitle
            ];
            return $contact;
        }, $persons) : null;


        if (!empty($this->data['contacts'])) {
            $this->data['contactTitle'] = __('Contact us', 'municipio');
        }
    }

    private function appendViewQuickFactsData(): void
    {
        $quickFacts = [];

        if (!empty($this->postMeta->grades)) {
            $gradeTerms = get_terms([
                'taxonomy' => 'grade',
                'include' => $this->postMeta->grades,
                'hide_empty' => false
            ]);

            if( !empty($gradeTerms) ) {
                $quickFacts[] = ['label' => $gradeTerms[0]->name];
            }
        }
        
        if (!empty($this->postMeta->area)) {
            $areaTerms = get_terms([
                'taxonomy' => 'area',
                'include' => $this->postMeta->area,
                'hide_empty' => false
            ]);

            if( !empty($areaTerms) ) {
                $quickFacts[] = ['label' => $areaTerms[0]->name];
            }
        }

        if (isset($this->postMeta->ownChef) && $this->postMeta->ownChef === true) {
            $quickFacts[] = ['label' => __('Own chef', 'municipio')];
        }

        if (!empty($this->postMeta->numberOfStudents)) {
            $value = absint($this->postMeta->numberOfStudents);
            $label = sprintf(__('Number of students: %d', 'municipio'), $value);
            $quickFacts[] = ['label' => $label];
        }

        if (!empty($quickFacts)) {
            $this->data['quickFactsTitle'] = __('Facts', 'municipio');
            $this->data['quickFacts'] = $quickFacts;
        }
    }

    private function appendViewAccordionData(): void
    {
        $accordions = [];
        $information = $this->postMeta->information;

        if (isset($information->how_we_work) && !empty($information->how_we_work)) {
            $accordions[] = ['list' => [
                [
                    'heading' => __('About the school', 'municipio'),
                    'content' => $information->about_us
                ]
            ]];   
        }

        if (isset($information->how_we_work) && !empty($information->how_we_work)) {
            $accordions[] = ['list' => [
                [
                    'heading' => __('How we work', 'municipio'),
                    'content' => $information->how_we_work
                ]
            ]];
        }

        if (isset($information->optional) && !empty($information->optional)) {
            foreach ($information->optional as $optional) {
                $accordions[] = ['list' => [
                    [
                        'heading' => $optional->heading ?? '',
                        'content' => $optional->content ?? ''
                    ]
                ]];
            }
        }

        $this->data['accordions'] = $accordions;
    }

    private function appendViewVisitingData(): void
    {
        $visitingAddresses = $this->postMeta->visitingAddress;
        $visitingData = [];
        $mapPins = [];

        if (!isset($visitingAddresses) || empty($visitingAddresses)) {
            return;
        }

        foreach ($visitingAddresses as $visitingAddress) {
            $visitingData[] = $visitingAddress->address;
            $mapPins[] = [
                'lat' => $visitingAddress->address->lat,
                'lng' => $visitingAddress->address->lng,
                'tooltip' => ['title' => $visitingAddress->address->address]
            ];
        }

        if (isset($visitingAddresses->address) && !empty($visitingAddresses->address)) {
            $visitingData[] = [
                'heading' => __('Visiting address', 'municipio'),
                'content' => $visitingAddress->address
            ];
        }

        if (!empty($visitingData)) {
            $this->data['visitingAddresses'] = $visitingData;
            $this->data['visitingAddressMapPins'] = $mapPins;
            $this->data['visitingAddressMapStartPosition'] = $this->getMapStartPosition($mapPins);
            $this->data['visitingDataTitle'] = sizeof($visitingData) === 1
                ? __('Visiting address', 'municipio')
                : __('Visiting addresses', 'municipio');
        }
    }

    private function getMapStartPosition(array $mapPins): array
    {
        $lats = array_map(fn ($pin) => $pin['lat'], $mapPins);
        $lngs = array_map(fn ($pin) => $pin['lng'], $mapPins);
        $maxLat = max($lats);
        $minLat = min($lats);
        $maxLng = max($lngs);
        $minLng = min($lngs);

        $startLat = $minLat + (($maxLat - $minLat) / 2);
        $startLng = $minLng + (($maxLng - $minLng) / 2);

        return ['lat' => $startLat, 'lng' => $startLng, 'zoom' => 13];
    }

    private function appendViewPagesData(): void
    {
        $pageIds = $this->postMeta->pages;

        if (!isset($pageIds) || empty($pageIds)) {
            return;
        }

        $pages = WP::getPosts([
            'post_type' => 'school_gpage',
            'post__in' => $pageIds,
            'suppress_filters' => false
        ]);

        if (!empty($pages)) {
            $this->data['pages'] = array_map(function ($page) {
                return [
                    'title' => $page->postTitle,
                    'content' => $page->postExcerpt,
                    'link' => $page->permalink,
                    'linkText' => __('Read more', 'municipio')
                ];
            }, $pages);
        }
    }

    private function appendSocialMediaLinksData(): void
    {
        $socialMediaLinks = [];

        if( !empty($this->postMeta->linkFacebook) ) {
            $socialMediaLinks[] = [
                'href' => $this->postMeta->linkFacebook,
                'text' => 'Facebook',
                'icon' => 'facebook'
            ];
        }

        if( !empty($this->postMeta->linkInstagram) ) {
            $socialMediaLinks[] = [
                'href' => $this->postMeta->linkInstagram,
                'text' => 'Instagram',
                'icon' => 'camera_alt'
            ];
        }

        if( !empty($socialMediaLinks) ) {
            $this->data['socialMediaLinksTitle'] = __('Follow us in social media', 'municipio');
        }

        $this->data['socialMediaLinks'] = $socialMediaLinks;
    }

    private function appendAttachmentsData(): void
    {

        $attachmentIds = array_merge(
            $this->postMeta->facadeImages ?: [],
            $this->postMeta->images ?: []
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

        $this->postMeta->attachments = $attachmentsById;
    }

    private function appendImagesData(): void
    {

        if (!isset($this->postMeta->attachments)) {
            return;
        }

        $facadeAttachments = array_filter($this->postMeta->attachments, function ($attachmentId) {
            return in_array($attachmentId, $this->postMeta->facadeImages ?: []);
        }, ARRAY_FILTER_USE_KEY);

        $environmentAttachments = array_filter($this->postMeta->attachments, function ($attachmentId) {
            return in_array($attachmentId, $this->postMeta->images ?: []);
        }, ARRAY_FILTER_USE_KEY);

        $this->data['facadeSliderItems'] = array_map([$this, 'attachmentToSliderItem'], $facadeAttachments);
        $this->data['environmentSliderItems'] = array_map([$this, 'attachmentToSliderItem'], $environmentAttachments);
    }

    private function attachmentToSliderItem($attachment): array
    {
        $sliderItem = [
            'title' => '',
            'layout' => 'center',
            'containerColor' => 'transparent',
            'textColor' => 'white',
            'heroStyle' => true
        ];

        if (str_contains($attachment->postMimeType, 'video')) {
            $sliderItem['background_video'] = $attachment->guid;
        } else {
            $sliderItem['desktop_image'] = $attachment->guid;
        }

        return $sliderItem;
    }
}
