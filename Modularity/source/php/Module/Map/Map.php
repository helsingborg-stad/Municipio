<?php

declare(strict_types=1);

namespace Modularity\Module\Map;

class Map extends \Modularity\Module
{
    private const DEFAULT_HEIGHT = 400;
    private const DEFAULT_LATITUDE = 59.329_32;
    private const DEFAULT_LONGITUDE = 18.068_58;
    private const DEFAULT_ZOOM = 10;
    private const TEMPLATE_DEFAULT = 'default';
    private const TEMPLATE_OPEN_STREET_MAP = 'openStreetMap';

    public $slug = 'map';
    public $supports = [];

    protected string $template = self::TEMPLATE_DEFAULT;

    /**
     * Configures module labels and ACF field filters.
     */
    public function init(): void
    {
        $this->nameSingular = __('Map', 'municipio');
        $this->namePlural = __('Maps', 'municipio');
        $this->description = __('Outputs an embedded map.', 'modularity');

        add_filter('acf/load_field/name=map_url', [$this, 'sslNotice']);
        add_filter('acf/load_value/name=map_url', [$this, 'filterMapUrl'], 10, 3);
        add_filter('acf/update_value/name=map_url', [$this, 'filterMapUrl'], 10, 3);
    }

    /**
     * Builds normalized view data for the selected map type.
     *
     * @return array<string, mixed>
     */
    public function data(): array
    {
        $fields = $this->getFields();
        $this->template = $this->normalizeMapType($fields['map_type'] ?? null);
        $sharedData = [
            'height' => $this->normalizeHeight($fields['height'] ?? null),
        ];

        return $this->template === self::TEMPLATE_OPEN_STREET_MAP ? $this->openStreetMapTemplateData($sharedData, $fields) : $this->defaultTemplateData($sharedData, $fields);
    }

    /**
     * Builds OpenStreetMap view data from normalized positions and valid markers.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $fields
     * @return array<string, mixed>
     */
    private function openStreetMapTemplateData(array $data, array $fields): array
    {
        $startPosition = $this->normalizePosition($fields['osm_start_position'] ?? null);
        $data['markers'] = $this->buildMarkers($fields['osm_markers'] ?? null);
        $data['lat'] = $startPosition['lat'] ?? self::DEFAULT_LATITUDE;
        $data['lng'] = $startPosition['lng'] ?? self::DEFAULT_LONGITUDE;
        $data['zoom'] = $startPosition['zoom'] ?? self::DEFAULT_ZOOM;

        return $data;
    }

    /**
     * Builds embedded-map view data from normalized ACF fields.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $fields
     * @return array<string, mixed>
     */
    private function defaultTemplateData(array $data, array $fields): array
    {
        $data['map_url'] = $this->normalizeMapUrl($fields['map_url'] ?? null);
        $data['map_description'] = $this->normalizeText($fields['map_description'] ?? null);

        $data['show_button'] = $this->normalizeBoolean($fields['show_button'] ?? false);
        $data['button_label'] = $this->normalizeText($fields['button_label'] ?? null);
        $data['button_url'] = $this->normalizeText($fields['button_url'] ?? null);
        $data['more_info_button'] = $this->normalizeBoolean($fields['more_info_button'] ?? false);
        $data['more_info'] = $this->normalizeText($fields['more_info'] ?? null);
        $data['more_info_title'] = $this->normalizeText($fields['more_info_title'] ?? null);

        $data['cardMapCss'] = $data['more_info_button'] ? 'o-grid-12@xs o-grid-8@md' : 'o-grid-12@md';
        $data['cardMoreInfoCss'] = $data['more_info_button'] ? 'o-grid-12@xs o-grid-4@md' : '';

        $data['uid'] = uniqid();
        $data['id'] = $this->ID;

        $data['lang'] = $this->getConsentLabels();

        return $data;
    }

    /**
     * Returns translated consent labels for embedded maps.
     *
     * @return array<string, array<string, string>>
     */
    protected function getConsentLabels(): array
    {
        return [
            'knownLabels' => [
                'title' => __('We need your consent to continue', 'municipio'),
                'info' => sprintf(
                    __(
                        'This part of the website shows content from %s. By continuing, <a href="%s"> you are accepting GDPR and privacy policy</a>.',
                        'municipio',
                    ),
                    '{SUPPLIER_WEBSITE}',
                    '{SUPPLIER_POLICY}',
                ),
                'button' => __('I understand, continue.', 'municipio'),
            ],
            'unknownLabels' => [
                'title' => __('We need your consent to continue', 'municipio'),
                'info' => sprintf(
                    __(
                        'This part of the website shows content from another website (%s). By continuing, you are accepting GDPR and privacy policy.',
                        'municipio',
                    ),
                    '{SUPPLIER_WEBSITE}',
                ),
                'button' => __('I understand, continue.', 'municipio'),
            ],
        ];
    }

    /**
     * Builds markers that contain valid coordinate data.
     *
     * @param mixed $markers
     * @return array<int, array{lat: float, lng: float, icon: string, content: string}>
     */
    private function buildMarkers(mixed $markers): array
    {
        if (!is_array($markers)) {
            return [];
        }

        $normalizedMarkers = [];
        foreach ($markers as $markerData) {
            if (!is_array($markerData)) {
                continue;
            }

            $position = $this->normalizePosition($markerData['position'] ?? null);
            if ($position === null) {
                continue;
            }

            $normalizedMarkers[] = [
                'lat' => $position['lat'],
                'lng' => $position['lng'],
                'icon' => 'location_on',
                'content' => $this->createMarkerTooltip($markerData),
            ];
        }

        return $normalizedMarkers;
    }

    /**
     * Renders tooltip markup for an OpenStreetMap marker.
     *
     * @param array<string, mixed> $marker
     */
    protected function createMarkerTooltip(array $marker = []): string
    {
        return render_blade_view(
            'partials.tooltip',
            [
                'marker' => $marker,
            ],
            [
                plugin_dir_path(__FILE__) . 'views',
            ],
        );
    }

    /**
     * Normalizes a supported map type.
     */
    private function normalizeMapType(mixed $mapType): string
    {
        return $mapType === self::TEMPLATE_OPEN_STREET_MAP ? self::TEMPLATE_OPEN_STREET_MAP : self::TEMPLATE_DEFAULT;
    }

    /**
     * Normalizes the map height to a positive pixel value.
     */
    private function normalizeHeight(mixed $height): int
    {
        return is_numeric($height) && (int) $height > 0 ? (int) $height : self::DEFAULT_HEIGHT;
    }

    /**
     * Normalizes coordinates and an optional zoom level.
     *
     * @return array{lat: float, lng: float, zoom?: int}|null
     */
    private function normalizePosition(mixed $position): ?array
    {
        if (!is_array($position) || !array_key_exists('lat', $position) || !array_key_exists('lng', $position) || !is_numeric($position['lat']) || !is_numeric($position['lng'])) {
            return null;
        }

        $normalizedPosition = [
            'lat' => (float) $position['lat'],
            'lng' => (float) $position['lng'],
        ];

        $zoom = $position['zoom'] ?? null;
        if (is_numeric($zoom)) {
            $normalizedPosition['zoom'] = (int) $zoom;
        }

        return $normalizedPosition;
    }

    /**
     * Normalizes an embedded map URL and enforces HTTPS.
     */
    private function normalizeMapUrl(mixed $mapUrl): string
    {
        if (!is_string($mapUrl)) {
            return '';
        }

        $mapUrl = trim($mapUrl);
        if (str_starts_with($mapUrl, '//')) {
            $mapUrl = 'https:' . $mapUrl;
        } else {
            $mapUrl = preg_replace('#^http://#i', 'https://', $mapUrl) ?? '';
        }

        return str_replace('disable_scroll=false', 'disable_scroll=true', $mapUrl);
    }

    /**
     * Normalizes text fields to strings.
     */
    private function normalizeText(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * Normalizes ACF true/false values without treating arbitrary strings as true.
     */
    private function normalizeBoolean(mixed $value): bool
    {
        return in_array($value, [true, 1, '1'], true);
    }

    /**
     * Adds HTTPS instructions to the map URL field when SSL is active.
     *
     * @param array<string, mixed> $field
     * @return array<string, mixed>
     */
    public function sslNotice(array $field): array
    {
        if (is_ssl() || $this->isUsingSSLProxy()) {
            $field['instructions'] =
                '<span style="color: #f00;">'
                . __(
                    'Your map link must start with http<strong>s</strong>://. Links without this prefix will not display.',
                    'modularity',
                )
                . '</span>';
        }

        return $field;
    }

    /**
     * Checks whether SSL is provided by a configured proxy.
     */
    private function isUsingSSLProxy(): bool
    {
        if (defined('SSL_PROXY') && constant('SSL_PROXY') === true) {
            return true;
        }

        return false;
    }

    /**
     * Filter the map URL value.
     *
     * @param mixed $value
     * @param mixed $postId
     * @param array<string, mixed> $field
     */
    public function filterMapUrl(mixed $value, mixed $postId, array $field): string
    {
        return is_string($value) ? htmlspecialchars_decode($value) : '';
    }

    /**
     * Returns the selected template with a default fallback.
     */
    public function template(): string
    {
        $path = __DIR__ . '/views/' . $this->template . '.blade.php';

        if (file_exists($path)) {
            return $this->template . '.blade.php';
        }

        return 'default.blade.php';
    }
}
