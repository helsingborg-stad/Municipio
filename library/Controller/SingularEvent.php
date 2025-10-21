<?php

namespace Municipio\Controller;

use DateTime;

/**
 * Class SingularEvent
 */
class SingularEvent extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view                       = 'single-schema-event';
    public const CURRENT_OCCASION_GET_PARAM   = 'startDate';
    public const CURRENT_OCCASION_DATE_FORMAT = 'Y-m-d_H:i';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->populateLanguageObject();

        $event = $this->post->getSchema();

        $this->data['description']           = (new SingularEvent\Mappers\MapDescription($this->wpService))->map($event);
        $this->data['priceListItems']        = (new SingularEvent\Mappers\MapPriceList($this->wpService))->map($event);
        $this->data['organizers']            = (new SingularEvent\Mappers\MapOrganizers($this->wpService))->map($event);
        $this->data['eventIsInThePast']      = (new SingularEvent\Mappers\MapEventIsInthePast($this->tryGetCurrentDateFromGetParam()))->map($event);
        $this->data['accessibilityFeatures'] = (new SingularEvent\Mappers\MapPhysicalAccessibilityFeatures())->map($event);
        $this->data['place']                 = (new SingularEvent\Mappers\MapPlace())->map($event);
        $this->data['occasions']             = (new SingularEvent\Mappers\MapOccasions($this->post->getPermalink(), $this->tryGetCurrentDateFromGetParam()))->map($event);
        $this->data['currentOccasion']       = (new SingularEvent\Mappers\MapCurrentOccasion(...$this->data['occasions']))->map($event);
        $this->data['icsUrl']                = (new SingularEvent\Mappers\MapIcsUrlFromOccasion($this->data['currentOccasion']))->map($event);
        $this->data['bookingLink']           = $this->post->getSchemaProperty('offers')[0]['url'] ?? null;
        $this->data['relatedPosts']          = $this->getRelatedEvents();

        // Ensure we are visiting a singular occasion if occasions exist
        (new SingularEvent\EnsureVisitingSingularOccasion\EnsureVisitingSingularOccasion(
            new SingularEvent\EnsureVisitingSingularOccasion\Redirect\Redirect($this->wpService),
            $this->tryGetCurrentDateFromGetParam(),
            ...$this->data['occasions']
        ))->ensureVisitingSingularOccasion();

        $this->trySetHttpStatusHeader($this->data['eventIsInThePast']);
    }

    /**
     * Get related events based on shared taxonomies
     *
     * @return array
     */
    public function getRelatedEvents(): array
    {
        global $wpdb;

        $terms = $this->post->getTerms(['event_keywords_name']);
        $termIds = array_map(fn($term) => $term->term_id, $terms);

        if (empty($termIds)) {
            return [];
        }

        $termIdsSql = implode(',', array_map('intval', $termIds));
        $maxResults = 6;
        // TODO: limit to 6 using a variable
        $results = $wpdb->get_results($wpdb->prepare(
            "
            SELECT p.ID, COUNT(*) AS shared_terms
            FROM {$wpdb->posts} AS p
            INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id
            INNER JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tt.term_id IN ($termIdsSql)
            AND p.post_status = 'publish'
            AND p.post_type = %s
            AND p.ID != %d
            GROUP BY p.ID
            ORDER BY shared_terms DESC, p.post_date DESC
            LIMIT {$maxResults}
            ",
            $this->post->getPostType(),
            $this->post->getId()
        ));


        $postIds = array_map(fn($post) => $post->ID, $results ?? []);
        $posts = $this->wpService->getPosts([
            'post__in' => $postIds,
            'posts_per_page' => $maxResults,
            'post_status' => 'publish',
            'post_type' => $this->post->getPostType(),
            'orderby' => 'post__in'
        ]);

        // $preparedPosts = 
        return $results ?: [];
    }

    /**
     * Populate the language object.
     */
    private function populateLanguageObject(): void
    {
        $this->data['lang']->description        = $this->wpService->__('Description', 'municipio');
        $this->data['lang']->addToCalendar      = $this->wpService->__('Add to calendar', 'municipio');
        $this->data['lang']->bookingTitle       = $this->wpService->__('Tickets & registration', 'municipio');
        $this->data['lang']->bookingButton      = $this->wpService->__('Go to booking page', 'municipio');
        $this->data['lang']->bookingDisclaimer  = $this->wpService->__('Tickets are sold according to the reseller.', 'municipio');
        $this->data['lang']->occasionsTitle     = $this->wpService->__('Date and time', 'municipio');
        $this->data['lang']->moreOccasions      = $this->wpService->__('More occasions', 'municipio');
        $this->data['lang']->placeTitle         = $this->wpService->__('Place', 'municipio');
        $this->data['lang']->directionsLabel    = $this->wpService->__('Get directions', 'municipio');
        $this->data['lang']->priceTitle         = $this->wpService->__('Price', 'municipio');
        $this->data['lang']->organizersTitle    = $this->wpService->__('Organizers', 'municipio');
        $this->data['lang']->accessibilityTitle = $this->wpService->__('Accessibility', 'municipio');
        $this->data['lang']->expiredEventNotice = $this->wpService->__('This event has already taken place.', 'municipio');
    }

    /**
     * Try to set HTTP status header
     * If the event is in the past, set 410 Gone
     */
    private function trySetHttpStatusHeader(bool $eventIsInThePast): void
    {
        if ($eventIsInThePast) {
            $this->wpService->statusHeader(410);
        }
    }

    private function tryGetCurrentDateFromGetParam(): ?DateTime
    {
        $startDateParam = $_GET[self::CURRENT_OCCASION_GET_PARAM] ?? null;

        if (empty($startDateParam)) {
            return null;
        }

        return DateTime::createFromFormat(self::CURRENT_OCCASION_DATE_FORMAT, $startDateParam) ?: null;
    }
}
