<?php

namespace Municipio\Api\PlaceSearch\Helper;

use WpService\Contracts\ApplyFilters;

class GetNeighbours
{
    public function __construct(private ApplyFilters $wpService)
    {
    }
    /**
     * Get neighbouring country codes for a given country code.
     *
     * @param string $countryCode The ISO country code.
     *
     * @return array An array of neighbouring country codes.
     */
    public function get(string $countryCode): array
    {
        $neighboursArray = [
            'ad' => ['es','fr'],
            'al' => ['me','gr','mk','rs'],
            'at' => ['de','cz','sk','hu','si','it','ch','li'],
            'ba' => ['hr','rs','me'],
            'be' => ['fr','nl','de','lu'],
            'bg' => ['ro','rs','mk','gr','tr'],
            'by' => ['pl','lt','lv','ru','ua'],
            'ch' => ['fr','de','at','li','it'],
            'cy' => [],
            'cz' => ['de','pl','sk','at'],
            'de' => ['dk','pl','cz','at','ch','fr','lu','be','nl'],
            'dk' => ['de','se'],
            'ee' => ['ru','lv'],
            'es' => ['pt','fr','ad','gi'],
            'fi' => ['se','no','ru'],
            'fr' => ['be','lu','de','ch','it','mc','ad','es'],
            'gb' => ['ie'],
            'gi' => ['es'],
            'gr' => ['al','mk','bg','tr'],
            'hr' => ['si','hu','rs','ba','me'],
            'hu' => ['at','sk','ua','ro','rs','hr','si'],
            'ie' => ['gb'],
            'is' => [],
            'it' => ['fr','ch','at','si','sm','va'],
            'li' => ['ch','at'],
            'lt' => ['lv','by','pl','ru'],
            'lu' => ['be','de','fr'],
            'lv' => ['ee','lt','by','ru'],
            'mc' => ['fr'],
            'md' => ['ro','ua'],
            'me' => ['hr','ba','rs','al'],
            'mk' => ['rs','bg','gr','al'],
            'mt' => [],
            'nl' => ['be','de'],
            'no' => ['se','fi','ru'],
            'pl' => ['de','cz','sk','ua','by','lt','ru'],
            'pt' => ['es'],
            'ro' => ['ua','md','bg','rs','hu'],
            'rs' => ['hu','ro','bg','mk','al','me','ba','hr'],
            'ru' => ['no','fi','ee','lv','lt','pl','by','ua'],
            'se' => ['no','fi','dk'],
            'si' => ['it','at','hu','hr'],
            'sk' => ['cz','pl','ua','hu','at'],
            'sm' => ['it'],
            'tr' => ['bg','gr'],
            'ua' => ['pl','sk','hu','ro','md','by','ru'],
            'va' => ['it'],
        ];

        return $this->wpService->applyFilters(
            'Municipio/Api/PlaceSearch/GetNeighbours',
            $neighboursArray[$countryCode] ?? [],
            $countryCode
        );
    }
}