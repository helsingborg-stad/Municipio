<?php

namespace Municipio\Api\PlaceSearch\Providers;

use Municipio\Api\PlaceSearch\Helper\GetNeighbours;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;
use WpService\Contracts\GetLocale;
use WpService\Contracts\ApplyFilters;
use Municipio\Schema\Schema;

/**
 * Class Openstreetmap
 *
 * @package Municipio\Api\PlaceSearch\Providers
 */
class Openstreetmap implements PlaceSearchProviderInterface
{
    /**
     * The OpenStreetMap Nominatim API URL.
     */
    private const API_SEARCH_URL         = 'https://nominatim.openstreetmap.org/search';
    private const API_REVERSE_SEARCH_URL = 'https://nominatim.openstreetmap.org/reverse';

    /**
     * Openstreetmap constructor.
     *
     * @param ApplyFilters&GetLocale&WpRemoteGet&IsWpError&WpRemoteRetrieveBody $wpService
     */
    public function __construct(
        private WpRemoteGet&IsWpError&WpRemoteRetrieveBody&GetLocale $wpService,
        private GetNeighbours $neighboursHelper
    ) {
    }

    /**
     * Search for a place using OpenStreetMap Nominatim API.
     *
     * @param string $query The search query.
     * @param array  $args  Additional arguments (not used).
     *
     * @return array An array of place data.
     */
    public function search(array $args = []): array
    {
        [$defaultCountryCodes, $defaultLanguage] = $this->getCountryCodesAndLanguage();
        $args['countrycodes']                    = $args['countrycodes'] ?? implode(',', $defaultCountryCodes);
        $args['accept-language']                 = $args['accept-language'] ?? $defaultLanguage;
        $data                                    = !empty($args['reverse']) ? $this->fetchReverseSearch($args) : $this->fetchSearch($args);

        if (empty($data)) {
            return [];
        }

        return $data;
    }

    /**
     * Get country codes and site language.
     *
     * @return array An array containing country codes and site language.
     */
    private function getCountryCodesAndLanguage(): array
    {
        static $countryCodes = null;
        static $siteLanguage = null;
        static $countryCode  = null;

        if (!$siteLanguage || !$countryCodes || !$countryCode) {
            $locale                       = $this->wpService->getLocale();
            [$siteLanguage, $countryCode] = explode('_', $locale);
            $countryCode                  = strtolower($countryCode);
            $siteLanguage                 = strtolower($siteLanguage);
            $countryCodes                 = $this->neighboursHelper->get($countryCode);
            $countryCodes[]               = $countryCode;
        }

        $countryCodesAndLanguage = $this->wpService->applyFilters(
            'Municipio/Api/PlaceSearch/GetLanguageAndCountryCodes',
            [$countryCodes, $siteLanguage],
            $countryCode
        );

        return is_array($countryCodesAndLanguage) ? $countryCodesAndLanguage : [[], ''];
    }

    /**
     * Fetch search results from OpenStreetMap Nominatim API.
     *
     * @param array $args The search query and additional arguments.
     *
     * @return array An array of transformed schema data.
     */
    public function fetchSearch(array $args = []): array
    {
        $response = $this->wpService->wpRemoteGet($this->createSearchEndpointUrl($args), [
            'headers' => [
                'User-Agent' => 'Municipio - getmunicipio.com'
            ]
        ]);

        if ($this->wpService->isWpError($response)) {
            return [];
        }

        $response = json_decode($this->wpService->wpRemoteRetrieveBody($response), true);

        return $this->transformResponseToSchema($response ?: []);
    }

    /**
     * Fetch reverse search results from OpenStreetMap Nominatim API.
     *
     * @param array $args The search query and additional arguments.
     *
     * @return array An array of transformed schema data.
     */
    public function fetchReverseSearch(array $args = []): array
    {
        $response = $this->wpService->wpRemoteGet(
            $this->createReverseSearchEndpointUrl($args),
            [
                'headers' => [
                    'User-Agent' => 'Municipio - getmunicipio.com'
                ]
            ]
        );

        if ($this->wpService->isWpError($response)) {
            return [];
        }

        $response = json_decode($this->wpService->wpRemoteRetrieveBody($response), true);

        return $this->transformResponseToSchema($response ? [$response] : [])[0] ?? [];
    }

    /**
     * Create the OpenStreetMap API reverse search endpoint URL.
     *
     * @param array $args The search query and additional arguments.
     *
     * @return string The complete API endpoint URL.
     */
    public function createReverseSearchEndpointUrl(array $args = []): string
    {
        $url = self::API_REVERSE_SEARCH_URL;

        $args = array_merge($args, [
            'format' => 'json',
            'lon'    => $args['lng'],
            'lat'    => $args['lat']
        ]);

        unset($args['lng']);
        unset($args['reverse']);

        return $url . '?' . http_build_query($args);
    }

    /**
     * Create the OpenStreetMap API endpoint URL.
     *
     * @param string $query The search query.
     * @param array  $args  Additional arguments.
     *
     * @return string The complete API endpoint URL.
     */
    public function createSearchEndpointUrl(array $args = []): string
    {
        $url = self::API_SEARCH_URL;

        $args = array_merge($args, [
            'format' => 'json'
        ]);

        return $url . '?' . http_build_query($args);
    }

    /**
     * Transform the OpenStreetMap response to a schema format.
     *
     * @param array $response The OpenStreetMap response.
     *
     * @return array An array of transformed schema data.
     */
    public function transformResponseToSchema(array $response): array
    {
        $schemaTransformedItems = [];
        foreach ($response as $value) {
            $streetAddress = ($address['road'] ?? '') . ' ' . ($address['house_number'] ?? '');
            $address       = $value['address'] ?? [];

            $postalAddress = Schema::postalAddress();
            $postalAddress->addressCountry($address['country'] ?? null);
            $postalAddress->addressLocality($address['city'] ?? null);
            $postalAddress->streetAddress(!empty(trim($streetAddress)) ? $streetAddress : null);
            $postalAddress->postalCode($address['postcode'] ?? null);
            $postalAddress->addressRegion($address['county'] ?? null);
            $postalAddress->addressLocality($address['municipality'] ?? null);
            $postalAddress->name($value['display_name'] ?? null);

            $postalAddress->toArray();

            $schema = Schema::place();
            $schema->latitude($value['lat'] ?? null);
            $schema->longitude($value['lon'] ?? null);
            $schema->address($postalAddress);
            $schema->name($value['name'] ?? ($value['display_name'] ?? null));

            $schemaTransformedItems[] = $schema->toArray();
        }
        return $schemaTransformedItems;
    }
}
