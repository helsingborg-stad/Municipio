<?php

namespace Municipio\Controller\SingularPlace;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Helper\Listing;
use Municipio\PostObject\PostObjectInterface;

class GetPlaceInfoList
{
    public static function getPlaceInfoList(PostObjectInterface $place): array
    {
        if ($place->getSchemaProperty('@type') !== 'Place') {
            return [];
        }

        $telephone = self::createTelephoneListingItems($place);
        $website = self::createWebsiteListingItems($place);
        $listing = array_merge($telephone, $website);

        return $listing;
    }

    private static function createTelephoneListingItems(PostObjectInterface $place): array
    {
        $telephoneData = EnsureArrayOf::ensureArrayOf($place->getSchemaProperty('telephone'), 'string');

        if (empty($telephoneData)) {
            return [];
        }

        return array_map(fn($phone) => Listing::createListingItem(
            $phone,
            '',
            ['src' => 'call'],
        ), $telephoneData);
    }

    private static function createWebsiteListingItems(PostObjectInterface $place): array
    {
        $websiteData = EnsureArrayOf::ensureArrayOf($place->getSchemaProperty('url'), 'string');

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