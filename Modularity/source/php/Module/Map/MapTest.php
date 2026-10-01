<?php

declare(strict_types=1);

namespace Modularity\Module\Map;

use PHPUnit\Framework\TestCase;

/**
 * Verifies normalization of Map module view data.
 */
class MapTest extends TestCase
{
    /**
     * Verifies predictable defaults and normalized embedded-map values.
     */
    public function testDataNormalizesDefaultMapFields(): void
    {
        // Arrange
        $map = $this->createMap([
            'map_type' => 'unsupported',
            'height' => 'invalid',
            'map_url' => '//example.com/map?disable_scroll=false',
            'map_description' => null,
            'show_button' => '1',
            'button_label' => 'Open map',
            'more_info_button' => 'false',
        ]);

        // Act
        $data = $map->data();

        // Assert
        static::assertSame('default.blade.php', $map->template());
        static::assertSame(400, $data['height']);
        static::assertSame('https://example.com/map?disable_scroll=true', $data['map_url']);
        static::assertSame('', $data['map_description']);
        static::assertTrue($data['show_button']);
        static::assertSame('Open map', $data['button_label']);
        static::assertFalse($data['more_info_button']);
        static::assertSame('o-grid-12@md', $data['cardMapCss']);
    }

    /**
     * Verifies safe OpenStreetMap defaults and malformed-marker handling.
     */
    public function testDataHandlesSparseOpenStreetMapFields(): void
    {
        // Arrange
        $map = $this->createMap([
            'map_type' => 'openStreetMap',
            'height' => '512',
            'osm_markers' => [
                null,
                [],
                ['position' => ['lat' => 'not-a-coordinate', 'lng' => 18]],
                ['title' => 'Null Island', 'position' => ['lat' => 0, 'lng' => '0']],
            ],
        ]);

        // Act
        $data = $map->data();

        // Assert
        static::assertSame('openStreetMap.blade.php', $map->template());
        static::assertSame(512, $data['height']);
        static::assertSame(59.329_32, $data['lat']);
        static::assertSame(18.068_58, $data['lng']);
        static::assertSame(10, $data['zoom']);
        static::assertSame(
            [
                [
                    'lat' => 0.0,
                    'lng' => 0.0,
                    'icon' => 'location_on',
                    'content' => 'Tooltip: Null Island',
                ],
            ],
            $data['markers'],
        );
    }

    /**
     * Verifies numeric start-position values are normalized for the map component.
     */
    public function testDataNormalizesOpenStreetMapStartPosition(): void
    {
        // Arrange
        $map = $this->createMap([
            'map_type' => 'openStreetMap',
            'osm_start_position' => [
                'lat' => '56.0465',
                'lng' => '12.6945',
                'zoom' => '13',
            ],
        ]);

        // Act
        $data = $map->data();

        // Assert
        static::assertSame(56.0465, $data['lat']);
        static::assertSame(12.6945, $data['lng']);
        static::assertSame(13, $data['zoom']);
        static::assertSame([], $data['markers']);
    }

    /**
     * Verifies the ACF URL filter accepts only strings.
     */
    public function testFilterMapUrlHandlesEncodedAndInvalidValues(): void
    {
        // Arrange
        $map = $this->createMap([]);

        // Act
        $decodedUrl = $map->filterMapUrl('https://example.com/?a=1&amp;b=2', 1, []);
        $invalidUrl = $map->filterMapUrl(false, 1, []);

        // Assert
        static::assertSame('https://example.com/?a=1&b=2', $decodedUrl);
        static::assertSame('', $invalidUrl);
    }

    /**
     * Creates a Map instance with deterministic ACF data and rendered content.
     *
     * @param array<string, mixed> $fields
     * @return Map
     */
    private function createMap(array $fields): Map
    {
        return new class($fields) extends Map {
            /**
             * @param array<string, mixed> $fields
             */
            public function __construct(
                private array $fields,
            ) {}

            /**
             * Returns the configured test fields.
             *
             * @return array<string, mixed>
             */
            protected function getFields(): array
            {
                return $this->fields;
            }

            /**
             * Returns deterministic tooltip markup for marker assertions.
             *
             * @param array<string, mixed> $marker
             */
            protected function createMarkerTooltip(array $marker = []): string
            {
                return 'Tooltip: ' . ($marker['title'] ?? '');
            }

            /**
             * Returns deterministic consent labels without WordPress translations.
             *
             * @return array<string, array<string, string>>
             */
            protected function getConsentLabels(): array
            {
                return [
                    'knownLabels' => [],
                    'unknownLabels' => [],
                ];
            }
        };
    }
}
