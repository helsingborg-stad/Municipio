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

        // Ensure we are visiting a singular occasion if occasions exist
        (new SingularEvent\EnsureVisitingSingularOccasion\EnsureVisitingSingularOccasion(
            new SingularEvent\EnsureVisitingSingularOccasion\Redirect\Redirect($this->wpService),
            $this->tryGetCurrentDateFromGetParam(),
            ...$this->data['occasions']
        ))->ensureVisitingSingularOccasion();

        $this->trySetHttpStatusHeader($this->data['eventIsInThePast']);
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
