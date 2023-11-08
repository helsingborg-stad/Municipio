<?php

namespace Municipio\Controller\ContentType\School;

use Municipio\Helper\Controller;
use Municipio\Helper\WP;
use WP_Post;

class SchoolDataPreparer implements DataPrepearerInterface
{

    private object $postMeta;
    private array $data;
    private const PAGE_POST_TYPE = 'school-page';
    private const PERSON_POST_TYPE = 'school-person';
    private const MEDIA_POST_TYPE = 'school-media';
    private const USP_TAXONOMY = 'school-usp';
    private const AREA_TAXONOMY = 'school-area';
    private const GRADE_TAXONOMY = 'school-grade';

    public function prepareData(array $data): array
    {
        $this->data = $data;
        $metaKeys = $this->getMetaKeys();
        $this->postMeta = new \stdClass();

        foreach ($metaKeys as $metaKey) {
            $fieldName = $this->getFieldName($metaKey);
            $this->postMeta->{$fieldName} = $this->getFieldValue($metaKey);
        }

        $this->appendImagesData();
        $this->appendViewNotificationData();
        $this->appendViewContactData();
        $this->appendViewQuickFactsData();
        $this->appendViewVisitUsData();
        $this->appendViewApplicationData();
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
        return WP::getField($fieldName, $this->data['post']->id, true);
    }

    private function getMetaKeys(): array
    {
        return [
            'area',
            'contacts',
            'cta_application',
            'custom_excerpt',
            'facade_images',
            'gallery',
            'grades',
            'information',
            'link_facebook',
            'link_instagram',
            'number_of_children',
            'number_of_profiles',
            'number_of_students',
            'number_of_units',
            'open_hours',
            'open_hours_leisure_center',
            'pages',
            'usp',
            'visiting_address',
            'notice_heading',
            'notice_content',
            'video',
            'visit_us'
        ];
    }

    private function appendViewNotificationData(): void
    {
        $notification = [];

        if (isset($this->postMeta->noticeHeading) && !empty($this->postMeta->noticeHeading)) {
            $notification['title'] = $this->postMeta->noticeHeading;
        }

        if (isset($this->postMeta->noticeContent) && !empty($this->postMeta->noticeContent)) {
            $notification['text'] = $this->postMeta->noticeContent;
        }

        $this->data['notification'] = empty($notification) ? null : $notification;
    }

    private function appendViewContactData(): void
    {
        if (!isset($this->postMeta->contacts) || !post_type_exists(self::PERSON_POST_TYPE)) {
            $this->data['contacts'] = null;
            return;
        }

        $personIds = array_map(function ($contact) {
            return $contact->person;
        }, $this->postMeta->contacts[0]);

        $persons = WP::getPosts(array('post_type' => self::PERSON_POST_TYPE, 'post__in' => $personIds, 'suppress_filters' => false));

        $this->data['contacts'] = !empty($persons) ? array_map(function ($contact) use ($persons) {

            $personPosts = get_posts(['post_type' => self::PERSON_POST_TYPE, 'post__in' => [$contact->person], 'suppress_filters' => false]);

            if( sizeof($personPosts) !== 1 ) {
                return null;
            }

            $featuredMediaID = WP::getField('featured_media', $personPosts[0]->ID);
            $email = WP::getField('e-mail', $personPosts[0]->ID);
            $phone = WP::getField('phone-number', $personPosts[0]->ID);

            $featuredMedia = $featuredMediaID ? WP::getPosts([
                'post_type' => self::MEDIA_POST_TYPE,
                'post__in' => [$featuredMediaID],
                'suppress_filters' => false
            ]) : null;

            $contact = (object)[
                'attachment'        => !empty($featuredMedia) ? $featuredMedia[0] : null,
                'professionalTitle' => $contact->professional_title ?? '',
                'email'             => $email,
                'phone'             => $phone,
                'name'              => $personPosts[0]->post_title
            ];
            return $contact;
        }, $this->postMeta->contacts[0]) : null;

        $this->data['contacts'] = array_filter($this->data['contacts'] ?? []);

        if (!empty($this->data['contacts'])) {
            $this->data['contactTitle'] = __('Contact us', 'municipio');
        }
    }

    private function appendViewQuickFactsData(): void
    {
        $quickFacts = [];

        if (!empty($this->postMeta->grades) && taxonomy_exists(self::GRADE_TAXONOMY)) {
            $gradeTerms = get_terms([
                'taxonomy' => self::GRADE_TAXONOMY,
                'include' => $this->postMeta->grades,
                'hide_empty' => false
            ]);

            if (!empty($gradeTerms)) {
                $quickFacts[] = ['label' => $gradeTerms[0]->name];
            }
        }

        if (!empty($this->postMeta->numberOfChildren)) {
            $value = absint($this->postMeta->numberOfChildren);
            $label = sprintf(__('Ca. %d children', 'municipio'), $value);
            $quickFacts[] = ['label' => $label];
        }
        
        if (!empty($this->postMeta->numberOfStudents)) {
            $value = absint($this->postMeta->numberOfStudents);
            $label = sprintf(__('Ca. %d students', 'municipio'), $value);
            $quickFacts[] = ['label' => $label];
        }

        if (!empty($this->postMeta->numberOfUnits)) {
            $value = absint($this->postMeta->numberOfUnits);
            $label = sprintf(__('%d units', 'municipio'), $value);
            $quickFacts[] = ['label' => $label];
        }

        if (!empty($this->postMeta->area) && taxonomy_exists(self::AREA_TAXONOMY)) {
            $areaTerms = get_terms([
                'taxonomy' => self::AREA_TAXONOMY,
                'include' => $this->postMeta->area,
                'hide_empty' => false
            ]);

            if (!empty($areaTerms) && !is_wp_error($areaTerms)) {
                $quickFacts[] = ['label' => $areaTerms[0]->name];
            }
        }

        if (!empty($this->postMeta->numberOfProfiles) ) {
            $value = absint($this->postMeta->numberOfProfiles);
            $label = sprintf(__('%s profiles', 'municipio'), $value);
            $quickFacts[] = ['label' => $label];
        }

        if (isset($this->postMeta->openHours) && !empty($this->postMeta->openHours)) {
            $open = substr($this->postMeta->openHours->open, 0, -2);
            $close = substr($this->postMeta->openHours->close, 0, -2);

            if (!empty($open) && !empty($close)) {
                $timeString = "$open - $close";
                $label = sprintf(__('Opening hours: %s', 'municipio'), $timeString);
                $quickFacts[] = ['label' => $label];
            }
        }

        if (isset($this->postMeta->openHoursLeisureCenter) && !empty($this->postMeta->openHoursLeisureCenter)) {
            $open = substr($this->postMeta->openHoursLeisureCenter->open, 0, -3);
            $close = substr($this->postMeta->openHoursLeisureCenter->close, 0, -3);

            if (!empty($open) && !empty($close)) {
                $timeString = "$open - $close";
                $label = sprintf(__('Leisure center: %s', 'municipio'), $timeString);
            } else {
                $label = sprintf(__('Leisure center', 'municipio'), $this->postMeta->openHoursLeisureCenter);
            }

            $quickFacts[] = ['label' => $label];
        }

        if (!empty($this->postMeta->usp) && taxonomy_exists(self::USP_TAXONOMY)) {

            // Get usp taxonomy terms
            $uspTerms = get_terms([
                'taxonomy' => self::USP_TAXONOMY,
                'include' => $this->postMeta->usp[0],
                'orderby' => 'include',
                'hide_empty' => false
            ]);

            // quickFacts may only contain 9 items totally. Append the appropriate amount of uspTerms to quickfacts.
            $uspTerms = array_slice($uspTerms, 0, 9 - sizeof($quickFacts));
            $quickFacts = array_merge($quickFacts, array_map(function ($uspTerm) {
                return ['label' => $uspTerm->name];
            }, $uspTerms));
        }

        // Split quickFacts into 3 columns
        $quickFacts = array_chunk($quickFacts, ceil(sizeof($quickFacts) / 3));

        if (!empty($quickFacts)) {
            $this->data['quickFactsTitle'] = __('Facts', 'municipio');
            $this->data['quickFacts'] = $quickFacts;
        }
    }

    private function appendViewVisitUsData(): void
    {
        $visitUs = null;

        if (isset($this->postMeta->visitUs) && !empty($this->postMeta->visitUs)) {
            $visitUs = [
                'title' => __('Visit us', 'municipio'),
                'content' => $this->postMeta->visitUs,
            ];
        }

        $this->data['visitUs'] = $visitUs;
    }

    private function appendViewApplicationData(): void
    {

        $defaultApplicationData = $this->getDefaultApplicationData($this->data['post']->postType);

        $this->data['application'] = [
            'title' => __('Are you interested in applying?', 'municipio'),
            'description' => $this->postMeta->ctaApplication->description ?? $defaultApplicationData['description'],
            'apply' => [
                'text' => $this->postMeta->ctaApplication->cta_apply_here->title ?? $defaultApplicationData['apply']['text'],
                'url' => $this->postMeta->ctaApplication->cta_apply_here->url ?? $defaultApplicationData['apply']['url']
            ],
            'howToApply' => [
                'text' => $this->postMeta->ctaApplication->cta_how_to_apply->title ?? $defaultApplicationData['howToApply']['text'],
                'url' => $this->postMeta->ctaApplication->cta_how_to_apply->url ?? $defaultApplicationData['howToApply']['url']
            ],
        ];
    }

    private function getDefaultApplicationData(string $postType): array
    {
        if ($postType === 'pre-school') {
            return [
                'description' => __('If you want to apply for a place in preschool, you can do so by filling in the form below. You can also apply for a place in preschool by contacting the preschool directly.', 'municipio'),
                'apply' => [
                    'text' => __('Apply here', 'municipio'),
                    'url' => '#'
                ],
                'howToApply' => [
                    'text' => __('How to apply', 'municipio'),
                    'url' => '#'
                ],
            ];
        }

        return [
            'description' => __('If you want to apply for a place in school, you can do so by filling in the form below. You can also apply for a place in school by contacting the school directly.', 'municipio'),
            'apply' => [
                'text' => __('Apply here', 'municipio'),
                'url' => '#'
            ],
            'howToApply' => [
                'text' => __('How to apply', 'municipio'),
                'url' => '#'
            ],
        ];
    }

    private function appendViewAccordionData(): void
    {
        $accordionListItems = [];
        $information = $this->postMeta->information;

        if (isset($information->how_we_work) && !empty($information->how_we_work)) {
            $accordionListItems[] = $this->getAccordionListItem(
                __('About the school', 'municipio'),
                $information->about_us
            );
        }

        if (isset($information->how_we_work) && !empty($information->how_we_work)) {
            $accordionListItems[] = $this->getAccordionListItem(
                __('How we work', 'municipio'),
                $information->how_we_work
            );
        }

        if (isset($information->orientation) && !empty($information->orientation)) {
            $accordionListItems[] = $this->getAccordionListItem(
                __('Orientation', 'municipio'),
                $information->orientation
            );
        }
        
        if (isset($information->our_leisure_center) && !empty($information->our_leisure_center)) {
            $accordionListItems[] = $this->getAccordionListItem(
                __('Our leisure center', 'municipio'),
                $information->our_leisure_center
            );
        }

        if (isset($information->optional) && !empty($information->optional)) {
            foreach ($information->optional as $optional) {

                if (empty($optional->heading) && empty($optional->content)) {
                    continue;
                }

                $accordionListItems[] = $this->getAccordionListItem(
                    $optional->heading,
                    $optional->content
                );
            }
        }

        $this->data['accordionListItems'] = $accordionListItems;
    }

    private function getAccordionListItem(?string $heading, ?string $text):array {
        return [
            'heading' => $heading ?? '',
            'content' => wpautop($text ?? '')
        ];
    }

    private function appendViewVisitingData(): void
    {
        $visitingAddresses = $this->postMeta->visitingAddress;
        $mapPins = [];

        if (!isset($visitingAddresses) || empty($visitingAddresses)) {
            return;
        }

        $visitingData = array_map(fn($visitingAddress) => $visitingAddress[0]->address, $visitingAddresses);
        $visitingData = array_map(function ($address) use(&$mapPins) {
            $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address[0]->address->address);
            $street = $address[0]->address->street_name ?? '';
            $streetNumber = $address[0]->address->street_number ?? '';
            $postCode = $address[0]->address->post_code ?? '';
            $city = $address[0]->address->city ?? '';
            $addressString = $street . ' ' . $streetNumber . ',<br>' . $postCode . ' ' . $city;
            $mapPinTooltip = ['title' => $this->data['post']->postTitle ?? null, 'excerpt' => $addressString];
            
            $mapPins[] = [
                'lat' => $address[0]->address->lat,
                'lng' => $address[0]->address->lng,
                'tooltip' => $mapPinTooltip
            ];
            
            return [
                'address' => $addressString,
                'description' => $address[0]->description ?? null,
                'mapsLink' => ['href' => $mapsUrl, 'text' => __('Find directions', 'municipio')]
            ];
        }, $visitingAddresses);

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
        if( !post_type_exists(self::PAGE_POST_TYPE) ) {
            $this->data['pages'] = null;
            return;
        }

        $pageIds = $this->postMeta->pages[0] ?? [];

        if (!isset($pageIds) || empty($pageIds)) {
            $this->data['pages'] = null;
            return;
        }

        $pages = WP::getPosts([
            'post_type' => self::PAGE_POST_TYPE,
            'post__in' => $pageIds,
            'suppress_filters' => false
        ]);

        $this->data['pages'] = array_map(function ($page) {
            return [
                'title' => $page->postTitle,
                'content' => $page->postExcerpt,
                'link' => $page->permalink,
                'linkText' => __('Read more', 'municipio')
            ];
        }, $pages);

        $this->data['pagesNumberOfColumns'] = sizeof($this->data['pages']) === 1 ? 12 : 6;
    }

    private function appendSocialMediaLinksData(): void
    {
        $socialMediaLinks = [];

        if (!empty($this->postMeta->linkFacebook)) {
            $socialMediaLinks[] = [
                'href' => $this->postMeta->linkFacebook,
                'text' => 'Facebook',
                'icon' => 'facebook'
            ];
        }

        if (!empty($this->postMeta->linkInstagram)) {
            $socialMediaLinks[] = [
                'href' => $this->postMeta->linkInstagram,
                'text' => 'Instagram',
                'icon' => 'camera_alt'
            ];
        }

        if (!empty($socialMediaLinks)) {
            $this->data['socialMediaLinksTitle'] = __('Follow us in social media', 'municipio');
        }

        $this->data['socialMediaLinks'] = $socialMediaLinks;
    }

    private function appendImagesData(): void
    {
        if( !empty($this->postMeta->facadeImages) ) {
            $facadeAttachmentIds = array_map(fn($facadeImage) => $facadeImage[0]->image->id ,$this->postMeta->facadeImages );

            $facadeAttachments = !empty($facadeAttachmentIds) ? WP::getPosts([
                'post_type' => self::MEDIA_POST_TYPE,
                'post__in' => $facadeAttachmentIds,
                'suppress_filters' => false
            ]) : [];
        }

        $this->data['facadeSliderItems'] = isset($facadeAttachments) && !empty($facadeAttachments)
            ? array_map([$this, 'attachmentToSliderItem'], $facadeAttachments)
            : null;

        if( !empty($this->postMeta->gallery) ) {
            $galleryAttachmentIds = array_map(fn($galleryImage) => $galleryImage->image->id ,$this->postMeta->gallery );

            $galleryAttachments = !empty($galleryAttachmentIds) ? WP::getPosts([
                'post_type' => self::MEDIA_POST_TYPE,
                'post__in' => $galleryAttachmentIds,
                'suppress_filters' => false
            ]) : [];
        }

        $this->data['gallerySliderItems'] = isset($galleryAttachments) && !empty($galleryAttachments)
            ? array_map([$this, 'attachmentToSliderItem'], $galleryAttachments)
            : null;

        $this->data['video'] =
        ((!isset($this->data['gallerySliderItems']) || empty($this->data['gallerySliderItems'])) && !empty($this->postMeta->video))
            ? $this->postMeta->video
            : null; 
    }

    private function attachmentToSliderItem($attachment): array
    {
        $sliderItem = [
            'title' => '',
            'layout' => 'bottom',
            'containerColor' => 'transparent',
            'textColor' => 'white',
            'heroStyle' => true
        ];

        $caption = WP::getField('caption', $attachment->id);
        if( $caption && !empty($caption->rendered) ) {
            $sliderItem['text'] = $caption->rendered;
        }
        
        $sliderItem['desktop_image'] = $attachment->guid;

        return $sliderItem;
    }
}
