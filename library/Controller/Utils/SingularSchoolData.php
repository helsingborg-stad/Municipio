<?php

namespace Municipio\Controller\Utils;

use Municipio\Helper\Controller;
use Municipio\Helper\WP;

/**
 * Class SchoolDataPreparer
 *
 * This class prepares data for the School content type.
 */
class SingularSchoolData
{
    private object $postMeta;
    private array $data;
    private const PAGE_POST_TYPE   = 'school-page';
    private const PERSON_POST_TYPE = 'school-person';
    private const USP_TAXONOMY     = 'school-usp';
    private const AREA_TAXONOMY    = 'school-area';
    private const GRADE_TAXONOMY   = 'school-grade';
    private const EVENT_POST_TYPE  = 'event';
    private const EVENT_TAXONOMY   = 'event_tags';

    /**
     * Prepare data for the School content type.
     *
     * @param array $data The data to be prepared.
     * @return array The prepared data.
     */
    public function prepareData(array $data): array
    {
        $this->data     = $data;
        $metaKeys       = $this->getMetaKeys();
        $this->postMeta = new \stdClass();

        foreach ($metaKeys as $metaKey) {
            $fieldName                    = $this->getFieldName($metaKey);
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
        $this->appendEventData();

        return $this->data;
    }

    /**
     * Get the field name.
     *
     * @param string $fieldName The field name.
     * @return string The formatted field name.
     */
    private function getFieldName(string $fieldName): string
    {
        return lcfirst(Controller::camelCase($fieldName));
    }

    /**
     * Get the field value.
     *
     * @param string $fieldName The field name.
     * @return mixed The field value.
     */
    private function getFieldValue($fieldName)
    {
        return WP::getField($fieldName, $this->data['post']->id, true);
    }

    /**
     * Get the meta keys.
     *
     * @return array The meta keys.
     */
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

    /**
     * Append view notification data.
     */
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

    /**
     * Append view contact data.
     */
    private function appendViewContactData(): void
    {
        if (!isset($this->postMeta->contacts) || !post_type_exists(self::PERSON_POST_TYPE)) {
            $this->data['contacts'] = null;
            return;
        }

        $personIds = array_map(function ($contact) {
            return $contact->person;
        }, $this->postMeta->contacts);

        $persons = WP::getPosts(array(
            'post_type'        => self::PERSON_POST_TYPE,
            'post__in'         => $personIds,
            'suppress_filters' => false
        ));

        $this->data['contacts'] = !empty($persons) ? array_map(function ($contact) {

            $personPosts = get_posts([
                'post_type'        => self::PERSON_POST_TYPE,
                'post__in'         => [$contact->person],
                'suppress_filters' => false
            ]);

            if (sizeof($personPosts) !== 1) {
                return null;
            }

            $featuredMediaUrl = WP::getThePostThumbnailUrl($personPosts[0]->ID, 'medium');
            $email            = WP::getField('e-mail', $personPosts[0]->ID);
            $phone            = WP::getField('phone-number', $personPosts[0]->ID);

            $contact = (object)[
                'imageSrc'          => $featuredMediaUrl ?: null,
                'professionalTitle' => $contact->professional_title ?? '',
                'email'             => $email,
                'phone'             => $phone,
                'name'              => $personPosts[0]->post_title
            ];
            return $contact;
        }, $this->postMeta->contacts) : null;

        $this->data['contacts'] = array_filter($this->data['contacts'] ?? []);

        if (!empty($this->data['contacts'])) {
            $this->data['contactTitle'] = __('Contact us', 'municipio');
        }
    }

    /**
     * Append view quick facts data.
     */
    private function appendViewQuickFactsData(): void
    {
        $quickFacts = [];

        $this->appendGradeTerms($quickFacts);
        $this->appendNumericFacts($quickFacts, 'numberOfChildren', __('Ca %d children', 'municipio'));
        $this->appendNumericFacts($quickFacts, 'numberOfStudents', __('Ca %d students', 'municipio'));
        $this->appendNumericFacts($quickFacts, 'numberOfUnits', __('%d units', 'municipio'));
        $this->appendAreaTerms($quickFacts);
        $this->appendNumericFacts($quickFacts, 'numberOfProfiles', __('%s profiles', 'municipio'));
        $this->appendOpenHours($quickFacts, 'openHours', __('Opening hours: %s', 'municipio'));
        $this->appendOpenHours($quickFacts, 'openHoursLeisureCenter', __('Leisure center: %s', 'municipio'));
        $this->appendUspTerms($quickFacts);

        // Split quickFacts into 3 columns
        $this->splitIntoColumns($quickFacts);

        if (!empty($quickFacts)) {
            $this->data['quickFactsTitle'] = __('Quick facts', 'municipio');
            $this->data['quickFacts']      = $quickFacts;
        } else {
            $this->data['quickFacts'] = false;
        }
    }

    /**
     * Appends grade terms to the given quick facts array.
     *
     * @param array $quickFacts The quick facts array to append grade terms to.
     * @return void
     */
    private function appendGradeTerms(&$quickFacts)
    {
        if (!empty($this->postMeta->grades) && taxonomy_exists(self::GRADE_TAXONOMY)) {
            $gradeTerms = wp_get_post_terms($this->data['post']->id, self::GRADE_TAXONOMY);
            if (!empty($gradeTerms) && !is_wp_error($gradeTerms)) {
                $quickFacts[] = ['label' => $gradeTerms[0]->name];
            }
        }
    }

    /**
     * Appends numeric facts to the given quick facts array.
     *
     * @param array $quickFacts The array to append the facts to.
     * @param string $metaKey The meta key to retrieve the numeric value from.
     * @param string $labelFormat The format string for the label of the fact.
     * @return void
     */
    private function appendNumericFacts(&$quickFacts, $metaKey, $labelFormat)
    {
        if (!empty($this->postMeta->$metaKey)) {
            $value        = absint($this->postMeta->$metaKey);
            $label        = sprintf($labelFormat, $value);
            $quickFacts[] = ['label' => $label];
        }
    }

    /**
     * Appends area terms to the given $quickFacts array.
     *
     * @param array $quickFacts The array to which the area terms will be appended.
     * @return void
     */
    private function appendAreaTerms(&$quickFacts)
    {
        if (!empty($this->postMeta->area) && taxonomy_exists(self::AREA_TAXONOMY)) {
            $areaTerms = wp_get_post_terms($this->data['post']->id, self::AREA_TAXONOMY);
            if (!empty($areaTerms) && !is_wp_error($areaTerms)) {
                $quickFacts[] = ['label' => $areaTerms[0]->name];
            }
        }
    }

    /**
     * Appends open hours to the quick facts array.
     *
     * This method takes the quick facts array, the open hours key, and the label format as parameters.
     * It appends the open hours to the quick facts array using the provided open hours key and label format.
     *
     * @param array $quickFacts The quick facts array to append the open hours to.
     * @param string $openHoursKey The key to use for the open hours in the quick facts array.
     * @param string $labelFormat The format to use for the label of the open hours in the quick facts array.
     * @return void
     */
    private function appendOpenHours(&$quickFacts, $openHoursKey, $labelFormat)
    {
        if (isset($this->postMeta->$openHoursKey) && !empty($this->postMeta->$openHoursKey)) {
            $timezone = new \DateTimeZone('GMT');
            $open     = wp_date('H:i', strtotime($this->postMeta->$openHoursKey->open), $timezone);
            $close    = wp_date('H:i', strtotime($this->postMeta->$openHoursKey->close), $timezone);

            if (!empty($open) && !empty($close)) {
                $timeString = "$open - $close";
                $label      = sprintf($labelFormat, $timeString);
            } else {
                $label = __('Leisure center', 'municipio'); // Adjust based on the context
            }

            $quickFacts[] = ['label' => $label];
        }
    }

    /**
     * Appends USP terms to the given quick facts array.
     *
     * @param array $quickFacts The array of quick facts to append USP terms to.
     * @return void
     */
    private function appendUspTerms(&$quickFacts)
    {
        if (!empty($this->postMeta->usp) && taxonomy_exists(self::USP_TAXONOMY)) {
            $uspTerms = wp_get_post_terms(
                $this->data['post']->id,
                self::USP_TAXONOMY,
                [
                    'orderby' => 'include',
                    'include' => $this->postMeta->usp
                ]
            );
            if (!empty($uspTerms) && !is_wp_error($uspTerms)) {
                $uspTerms = array_slice($uspTerms, 0, 9 - count($quickFacts));
                foreach ($uspTerms as $uspTerm) {
                    $quickFacts[] = ['label' => $uspTerm->name];
                }
            }
        }
    }

    /**
     * Splits the given array of quick facts into columns.
     *
     * @param array $quickFacts The array of quick facts to be split into columns.
     * @return void
     */
    private function splitIntoColumns(&$quickFacts)
    {
        $numberOfColumns = ceil(count($quickFacts) / 3);
        if ($numberOfColumns > 0) {
            $quickFacts = array_chunk($quickFacts, $numberOfColumns);
        }
    }


    /**
     * Appends data for the "Visit Us" section.
     */
    private function appendViewVisitUsData(): void
    {
        $visitUs = null;

        if (isset($this->postMeta->visitUs) && !empty($this->postMeta->visitUs)) {
            $visitUs = [
                'title'   => __('Visit us', 'municipio'),
                'content' => $this->postMeta->visitUs,
            ];
        }

        $this->data['visitUs'] = $visitUs;
    }

    /**
     * Appends data for the "Application" section.
     */
    private function appendViewApplicationData(): void
    {
        $this->data['application'] = [];

        // Using null-coalescing operator to simplify the assignment
        $this->data['application']['displayOnWebsite'] =
            !empty($this->postMeta->ctaApplication->display_on_website) ? (bool) $this->postMeta->ctaApplication->display_on_website : true;

        $this->data['application']['title'] = !empty($this->postMeta->ctaApplication->title) ? $this->postMeta->ctaApplication->title : $this->getApplicationCtaTitle(get_queried_object());

        $this->data['application']['description'] = !empty($this->postMeta->ctaApplication->description) ? $this->postMeta->ctaApplication->description : '';

        $this->data['application']['apply']      = null;
        $this->data['application']['howToApply'] = null;

        if (
            !empty($this->postMeta->ctaApplication->cta_apply_here->url) &&
            !empty($this->postMeta->ctaApplication->cta_apply_here->title)
        ) {
            $this->data['application']['apply'] = [
                'text' => $this->postMeta->ctaApplication->cta_apply_here->title,
                'url'  => $this->postMeta->ctaApplication->cta_apply_here->url,
            ];
        }

        if (
            !empty($this->postMeta->ctaApplication->cta_how_to_apply->url) &&
            !empty($this->postMeta->ctaApplication->cta_how_to_apply->title)
        ) {
            $this->data['application']['howToApply'] = [
                'text' => $this->postMeta->ctaApplication->cta_how_to_apply->title,
                'url'  => $this->postMeta->ctaApplication->cta_how_to_apply->url,
            ];
        }
    }
    /**
     * Retrieves the application title based on the post type or a default value.
     *
     * @param \WP_Post|null $post The post object.
     * @return string The application title or the default value.
     */
    private function getApplicationCtaTitle($post): string
    {
        if ($post instanceof \WP_Post) {
            if ($post->post_type === 'pre-school') {
                // Code for 'pre-school' post type
                return sprintf(_x(
                    'Do you want to join %s?',
                    'Shown on pre-schools',
                    'municipio'
                ), $post->post_title);
            } else {
                // Code for other post types
                return sprintf(__('Do you want to apply to %s?', 'municipio'), $post->post_title);
            }
        }

        return __('Do you want to apply?', 'municipio');
    }
    /**
     * Appends data for the accordion section.
     */
    private function appendViewAccordionData(): void
    {
        $accordionListItems = [];
        $information        = !empty($this->postMeta->information) ? $this->postMeta->information : false;

        if (!$information) {
            return;
        }

        // Consolidated adding of accordion items with checks for existence
        $this->addAccordionItemIfExists(
            $accordionListItems,
            $information->how_we_work ?? null,
            __('About the school', 'municipio'),
            $information->about_us ?? null
        );
        $this->addAccordionItemIfExists(
            $accordionListItems,
            $information->how_we_work ?? null,
            __('How we work', 'municipio'),
            $information->how_we_work ?? null
        );
        $this->addAccordionItemIfExists(
            $accordionListItems,
            $information->orientation ?? null,
            __('Orientation', 'municipio'),
            $information->orientation ?? null
        );
        $this->addAccordionItemIfExists(
            $accordionListItems,
            $information->our_leisure_center ?? null,
            __('Our leisure center', 'municipio'),
            $information->our_leisure_center ?? null
        );

        // Process optional information if it exists and is not empty
        if (!empty($information->optional)) {
            foreach ($information->optional as $optional) {
                // Ensure optional item has both heading and content
                if (isset($optional->heading) && isset($optional->content)) {
                    $this->addAccordionItemIfExists($accordionListItems, $optional, $optional->heading, $optional->content);
                }
            }
        }

        $this->data['accordionListItems'] = $accordionListItems;
    }

    /**
     * Adds an accordion item to the list if the given property exists and is not empty.
     * @param array $accordionListItems The array of accordion list items.
     * @param mixed $property The property to check for existence and non-emptiness.
     * @param string $heading The heading for the accordion item.
     * @param string $content The content for the accordion item.
     * @return void
     */
    private function addAccordionItemIfExists(&$accordionListItems, $property, ?string $heading, ?string $content): void
    {
        if (isset($property) && !empty($property)) {
            $accordionListItems[] = $this->getAccordionListItem($heading, $content);
        }
    }

    /**
     * Returns an array representing an accordion list item.
     *
     * @param string|null $heading The heading of the accordion list item.
     * @param string|null $text The content of the accordion list item.
     * @return array The accordion list item array.
     */
    private function getAccordionListItem(?string $heading, ?string $text): array
    {
        return [
            'heading' => $heading ?? '',
            'content' => wpautop($text ?? ''),
            'anchor'  => sanitize_title($heading),
        ];
    }

    /**
     * Appends data for the visiting section.
     */
    private function appendViewVisitingData(): void
    {
        $visitingAddresses = $this->postMeta->visitingAddress;
        $mapPins           = [];

        if (!isset($visitingAddresses) || empty($visitingAddresses)) {
            return;
        }

        $visitingData = array_map(
            fn($visitingAddress) => $visitingAddress->address,
            $visitingAddresses
        );
        $visitingData = array_map(function ($address) use (&$mapPins, $visitingAddresses) {
            $mapsUrl       = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address->address->address);
            $street        = $address->address->street_name ?? '';
            $streetNumber  = $address->address->street_number ?? '';
            $postCode      = $address->address->post_code ?? '';
            $city          = $address->address->city ?? '';
            $lineBreak     = sizeof($visitingAddresses) > 1 ? ', <br>' : ', ';
            $addressString = $street . ' ' . $streetNumber . $lineBreak . $postCode . ' ' . $city;
            $mapPinTooltip = [
                'title'      => $this->data['post']->postTitle ?? null,
                'excerpt'    => $addressString,
                'directions' => ['label' => __('Find directions', 'municipio'), 'url' => $mapsUrl]
            ];

            $mapPins[] = [
                'lat'     => $address->address->lat,
                'lng'     => $address->address->lng,
                'tooltip' => $mapPinTooltip
            ];

            return [
                'address'     => $addressString,
                'description' => $address->description ?? null,
                'mapsLink'    => ['href' => $mapsUrl, 'text' => __('Find directions', 'municipio')]
            ];
        }, $visitingAddresses);

        $this->data['visitingAddresses']      = !empty($visitingData) ? $visitingData : false;
        $this->data['visitingAddressMapPins'] = !empty($mapPins) ? $mapPins : false;

        $this->data['visitingAddressMapStartPosition'] =
        !empty($mapPins) ? $this->getMapStartPosition($mapPins) : false;

        if (sizeof($visitingData) <= 1) {
            $this->data['visitingDataTitle'] = __('Visiting address', 'municipio');
        } else {
            $this->data['visitingDataTitle'] = __('Visiting addresses', 'municipio');
        }
    }

    /**
     * Returns the start position for the map based on the given map pins.
     *
     * @param array $mapPins The array of map pins.
     * @return array The start position for the map.
     */
    private function getMapStartPosition(array $mapPins): array
    {
        $lats   = array_map(fn ($pin) => (float) $pin['lat'], $mapPins);
        $lngs   = array_map(fn ($pin) => (float) $pin['lng'], $mapPins);
        $maxLat = max($lats);
        $minLat = min($lats);
        $maxLng = max($lngs);
        $minLng = min($lngs);

        $startLat = $minLat + (($maxLat - $minLat) / 2);
        $startLng = $minLng + (($maxLng - $minLng) / 2);

        return ['lat' => $startLat, 'lng' => $startLng, 'zoom' => 13];
    }

    /**
     * Appends data for the pages section.
     */
    private function appendViewPagesData(): void
    {
        if (!post_type_exists(self::PAGE_POST_TYPE)) {
            $this->data['pages'] = null;
            return;
        }

        $pageIds = $this->postMeta->pages ?? [];

        if (!isset($pageIds) || empty($pageIds)) {
            $this->data['pages'] = null;
            return;
        }

        $pages = WP::getPosts([
            'post_type'        => self::PAGE_POST_TYPE,
            'post__in'         => $pageIds,
            'suppress_filters' => false
        ]);

        $this->data['pages'] = array_map(function ($page) {
            return [
                'title'    => $page->postTitle,
                'content'  => $page->postExcerpt,
                'link'     => $page->permalink,
                'linkText' => __('Read more', 'municipio')
            ];
        }, $pages);

        $this->data['pagesNumberOfColumns'] = sizeof($this->data['pages']) === 1 ? 12 : 6;
    }

    /**
     * Appends data for the social media links section.
     */
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
                'icon' => 'instagram'
            ];
        }

        if (!empty($socialMediaLinks)) {
            $this->data['socialMediaLinksTitle'] = __('Follow us in social media', 'municipio');
        }

        $this->data['socialMediaLinks'] = $socialMediaLinks;
    }

    /**
     * Maps the attachments data.
     *
     * @param mixed $attachments The attachments data.
     * @return array|null The mapped attachments.
     */
    private function mapAttachments($attachments): ?array
    {
        if (empty($attachments)) {
            return null;
        }

        return array_map(function ($attachment) {
            $imageSize    = [760];
            $attachmentId = $attachment->image->id;
            $postType     = $this->data['post']->postType;
            $src          = WP::getAttachmentImageSrc($attachmentId, $imageSize, false, $postType);
            $caption      = WP::getAttachmentCaption($attachmentId, $postType);
            return [
                'src'     => is_array($src) ? $src[0] : '',
                'caption' => $caption
            ];
        }, $attachments);
    }

    /**
     * Appends data for the images section.
     */
    private function appendImagesData(): void
    {
        $facadeAttachments  = $this->mapAttachments($this->postMeta->facadeImages);
        $galleryAttachments = $this->mapAttachments($this->postMeta->gallery);

        $this->data['facadeSliderItems'] = !empty($facadeAttachments)
            ? array_map([$this, 'attachmentToSliderItem'], $facadeAttachments)
            : null;

        $this->data['gallerySliderItems'] = !empty($galleryAttachments)
            ? array_map([$this, 'attachmentToSliderItem'], $galleryAttachments)
            : null;

        $this->data['video'] =
            ((!isset($this->data['gallerySliderItems']) || empty($this->data['gallerySliderItems'])) &&
            !empty($this->postMeta->video))
                ? wp_oembed_get($this->postMeta->video)
                : null;
    }

    /**
     * Converts an attachment to a slider item.
     *
     * @param array $attachment The attachment data.
     * @return array The slider item.
     */
    private function attachmentToSliderItem($attachment): array
    {
        $sliderItem = [
            'title'          => '',
            'layout'         => 'bottom',
            'containerColor' => 'transparent',
            'heroStyle'      => true,
            'classList'      => ['u-margin__bottom--0', 'u-padding__bottom--0', 'u-margin__top--0', 'u-padding__top--0']
        ];

        $sliderItem['text']  = $attachment['caption'];
        $sliderItem['image'] = $attachment['src'];

        return $sliderItem;
    }

    /**
     * Appends data for the event section.
     */
    private function appendEventData()
    {
        $this->data['events']      = null;
        $this->data['eventsTitle'] = null;

        if (!post_type_exists(self::EVENT_POST_TYPE) || !taxonomy_exists(self::EVENT_TAXONOMY)) {
            return;
        }

        $eventTags = get_terms(['taxonomy' => self::EVENT_TAXONOMY, 'search' => $this->data['post']->postTitle]);

        if (empty($eventTags)) {
            return;
        }

        $eventTag = $eventTags[0];
        $events   = get_posts([
            'post_type' => self::EVENT_POST_TYPE,
            'tax_query' => [
                [
                    'taxonomy' => self::EVENT_TAXONOMY,
                    'field'    => 'term_id',
                    'terms'    => [$eventTag->term_id]
                ]
            ]
        ]);

        if (empty($events)) {
            return;
        }

        foreach ($events as $event) {
            $title     = $event->post_title;
            $text      = $event->post_content;
            $occasions = get_post_meta($event->ID, 'occasions_complete', true);


            if (empty($occasions) || empty($text) || empty($text)) {
                continue;
            }

            $firstOccation = $occasions[0];

            if (
                !isset($firstOccation['start_date']) ||
                empty($firstOccation['start_date']) ||
                strtotime($firstOccation['start_date']) === false
            ) {
                continue;
            }

            $timestamp = strtotime($firstOccation['start_date']);
            $time      = wp_date('H:i', $timestamp, new \DateTimeZone('GMT'));
            $date      = wp_date('Y-m-d', $timestamp, new \DateTimeZone('GMT'));
            $dateLong  = wp_date(get_option('date_format'), $timestamp, new \DateTimeZone('GMT'));

            if ($this->data['events'] === null) {
                $this->data['events'] = [];
            }

            $this->data['events'][] = [
            'title'       => $title,
            'text'        => $text,
            'date'        => $date,
            'time'        => $time,
            'dateAndTime' =>
            sprintf(
                __("Date and time: %s at %s", 'municipio'),
                $dateLong,
                $time
            )
            ];
        }

        if ($this->data['events'] !== null) {
            $this->data['eventsTitle'] = __('Events', 'municipio');
        }
    }
}
