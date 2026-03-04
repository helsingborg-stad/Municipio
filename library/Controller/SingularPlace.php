<?php

namespace Municipio\Controller;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Helper\Listing;

/**
 * Class SingularPlace
 *
 * Used to represent physical places.
 */
class SingularPlace extends \Municipio\Controller\Singular
{
    public string $view = 'single-schema-place';

    public function init()
    {
        parent::init();

        $pageID = $this->getPageID();

        $this->data['relatedPosts'] = $this->getRelatedPosts($pageID);
        $this->data['placeInfoList'] = $this->createPlaceInfoList();
        $this->data['placeActions'] = $this->createActions($pageID);
    }

    private function createActions(): array
    {
        $tourBookingPage = $this->createTourBookingPageAction();

        return $this->wpService->applyFilters('Municipio/Controller/SingularPlace/actions', $tourBookingPage, $this->post);
    }

    private function createPlaceInfoList(): array
    {
        $telephone = $this->createTelephoneListingItems();
        $website = $this->createWebsiteListingItems();
        $listing = array_merge($telephone, $website);

        return $this->wpService->applyFilters('Municipio/Controller/SingularPlace/placeInfoList', $listing, $this->post);
    }

    private function createTourBookingPageAction(): array
    {
        $tourBookingPage = EnsureArrayOf::ensureArrayOf($this->post->getSchemaProperty('tourBookingPage'), 'string');

        $formattedTourBookingPages = [];
        foreach ($tourBookingPage as $index => $bookingLink) {
            $formattedTourBookingPages[] = [
                'text' => $this->wpService->__('Book here', 'municipio'),
                'href' => $bookingLink,
                'color' => 'primary',
                'style' => 'filled',
                'classList' => ['u-width--100'],
            ];
        }

        return $formattedTourBookingPages;
    }

    private function createTelephoneListingItems(): array
    {
        $telephoneData = EnsureArrayOf::ensureArrayOf($this->post->getSchemaProperty('telephone'), 'string');

        if (empty($telephoneData)) {
            return [];
        }

        return array_map(fn($phone) => Listing::createListingItem(
            $phone,
            '',
            ['src' => 'call'],
        ), $telephoneData);
    }

    private function createWebsiteListingItems(): array
    {
        $websiteData = EnsureArrayOf::ensureArrayOf($this->post->getSchemaProperty('url'), 'string');

        if (empty($websiteData)) {
            return [];
        }

        return array_map(fn($url) => Listing::createListingItem(
            $url,
            $url,
            ['src' => 'language'],
        ), $websiteData);
    }
}
