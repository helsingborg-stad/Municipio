<?php

declare(strict_types=1);

namespace Municipio\Theme\Footer;

use WpService\Contracts\GetThemeMod;

/**
 * Resolves the footer widget areas and the number of columns they are rendered in.
 *
 * All footer areas are always registered and rendered when they contain widgets.
 * The design tool only decides how many columns the areas are laid out in.
 */
class FooterAreas
{
    /**
     * Number of columns used when the design tool has no stored column count.
     *
     * Matches the styleguide default for --c-footer--columns-count.
     */
    public const DEFAULT_COLUMN_COUNT = 3;

    /**
     * Total number of registered footer widget areas.
     */
    public const AREA_COUNT = 6;

    private const COLUMN_COUNT_TOKEN = '--c-footer--columns-count';

    public function __construct(private GetThemeMod $wpService)
    {
    }

    /**
     * Get all registered footer widget area ids.
     *
     * @return array<int, string>
     */
    public function getAreaIds(): array
    {
        $areaIds = [];

        for ($index = 0; $index < self::AREA_COUNT; $index++) {
            $areaIds[] = $this->getAreaId($index);
        }

        return $areaIds;
    }

    /**
     * Get the widget area id for a given footer area index.
     */
    public function getAreaId(int $index): string
    {
        return $index === 0 ? 'footer-area' : 'footer-area-column-' . $index;
    }

    /**
     * Get the configured footer column count from stored design tokens.
     */
    public function getColumnCount(): int
    {
        $storedTokens = $this->wpService->getThemeMod('tokens', '');

        if (!is_string($storedTokens) || trim($storedTokens) === '') {
            return self::DEFAULT_COLUMN_COUNT;
        }

        $decodedTokens = json_decode($storedTokens, true);

        if (!is_array($decodedTokens)) {
            return self::DEFAULT_COLUMN_COUNT;
        }

        $columnCount = $decodedTokens['component']['__general__']['footer'][self::COLUMN_COUNT_TOKEN] ?? null;

        if (!is_numeric($columnCount)) {
            return self::DEFAULT_COLUMN_COUNT;
        }

        return min(self::AREA_COUNT, max(1, (int) $columnCount));
    }
}
