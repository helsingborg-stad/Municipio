<?php

declare(strict_types=1);

namespace Municipio\Upgrade\Palette;

use AcfService\Contracts\GetField;
use Municipio\Styleguide\Customize\TokenData\ColorTokenDefinitions;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostField;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\UpdateOption;

/** Seeds empty palette slots once, before literal content colors are migrated. */
class PopulateUsedColors
{
    private const OPTION = 'municipio_used_color_palette_seeded';
    // Oklab distance: a small perceptual difference, rather than a fixed
    // per-channel RGB difference that varies with hue and brightness.
    private const SIMILARITY_THRESHOLD = 0.025;

    public function __construct(
        private GetOption&UpdateOption&GetPosts&GetPostTypes&GetPostMeta&GetPostField&GetThemeMod&SetThemeMod $wp,
        private GetField $acf,
    ) {}

    public function run(): void
    {
        if ($this->wp->getOption(self::OPTION, false)) {
            return;
        }
        $stored = $this->wp->getThemeMod('tokens', '{}');
        $tokens = is_array($stored) ? $stored : json_decode((string) $stored, true);
        if (!is_array($tokens)) {
            throw new \RuntimeException('Cannot seed the palette: stored design tokens are invalid.');
        }
        $counts = [];
        foreach ($this->wp->getPosts(['post_type' => ['mod-section-full', 'mod-section-split', 'mod-section-featured'], 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids']) as $id) {
            $this->countColor($counts, $this->wp->getPostMeta((int) $id, 'background_color', true));
        }
        foreach ($this->wp->getPosts(['post_type' => 'mod-manualinput', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids']) as $id) {
            $this->countManualRows($counts, $this->acf->getField('manual_inputs', (int) $id, false));
        }
        $types = array_values(array_diff($this->wp->getPostTypes(), ['revision']));
        if ($types !== []) {
            foreach ($this->wp->getPosts(['post_type' => $types, 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids']) as $id) {
                $content = $this->wp->getPostField('post_content', (int) $id, 'raw');
                if (str_contains($content, 'wp:acf/')) {
                    $this->countBlocks($counts, parse_blocks($content));
                }
            }
        }
        $new = $this->populate($tokens, $counts);
        if ($new !== $tokens && !$this->wp->setThemeMod('tokens', json_encode($new, JSON_THROW_ON_ERROR))) {
            throw new \RuntimeException('Could not save the discovered palette colors.');
        }
        if (!$this->wp->updateOption(self::OPTION, 1, false) && !$this->wp->getOption(self::OPTION, false)) {
            throw new \RuntimeException('Could not record palette discovery completion.');
        }
    }

    /** @param array<string, int> $counts */
    public function countBlocks(array &$counts, array $blocks): void
    {
        foreach ($blocks as $block) {
            $name = $block['blockName'] ?? '';
            $data = $block['attrs']['data'] ?? [];
            if (is_array($data)) {
                if (in_array($name, ['acf/section-full', 'acf/section-split', 'acf/section-featured'], true)) {
                    $this->countColor($counts, $data['background_color'] ?? $data['field_61543393334bf'] ?? $data['field_60d1a9295551b'] ?? null);
                } elseif ($name === 'acf/container') {
                    $this->countColor($counts, $data['color'] ?? $data['field_63cfdc219a6d3'] ?? null);
                    $rows = $data['background_gradient'] ?? $data['field_648329b62db2e'] ?? null;
                    if (is_array($rows)) {
                        foreach ($rows as $row) {
                            if (is_array($row)) {
                                $this->countColor($counts, $row['color'] ?? $row['field_64832a1b2db2f'] ?? null);
                            }
                        }
                    } else {
                        foreach ($data as $key => $value) {
                            if (is_string($key) && preg_match('/^background_gradient_\d+_color$/', $key)) {
                                $this->countColor($counts, $value);
                            }
                        }
                    }
                } elseif ($name === 'acf/manualinput') {
                    $rows = $data['manual_inputs'] ?? $data['field_64ff22b2d91b7'] ?? null;
                    if (is_array($rows)) {
                        $this->countManualRows($counts, $rows);
                    } else {
                        foreach ($data as $key => $value) {
                            if (is_string($key) && preg_match('/^manual_inputs_\d+_custom_background_color$/', $key)) {
                                $this->countColor($counts, $value);
                            }
                        }
                    }
                }
            }
            if (is_array($block['innerBlocks'] ?? null)) {
                $this->countBlocks($counts, $block['innerBlocks']);
            }
        }
    }

    private function countManualRows(array &$counts, mixed $rows): void
    {
        if (!is_array($rows)) {
            return;
        }
        foreach ($rows as $row) {
            if (is_array($row)) {
                $this->countColor($counts, $row['custom_background_color'] ?? $row['field_689b2cf733d44'] ?? null);
            }
        }
    }

    private function countColor(array &$counts, mixed $value): void
    {
        $color = $this->normalize($value);
        if ($color !== null) {
            $counts[$color] = ($counts[$color] ?? 0) + 1;
        }
    }

    /** @param array<string, int> $counts */
    public function populate(array $tokens, array $counts): array
    {
        $values = is_array($tokens['token'] ?? null) ? $tokens['token'] : [];
        $pairs = ColorTokenDefinitions::getBrandPairs();
        $existing = [];
        foreach ($pairs as $pair) {
            $color = $this->normalize($values[$pair['background']] ?? ($pair['optional'] ? null : ColorTokenDefinitions::getDefault($pair['background'])));
            if ($color !== null) {
                $existing[$color] = true;
            }
        }
        // The most frequent shade is the representative of each group; count
        // all similar shades before deciding which groups deserve a slot.
        $sort = static fn(array &$items) => uksort($items, static fn(string $a, string $b): int => ($items[$b] <=> $items[$a]) ?: strcmp($a, $b));
        $sort($counts);
        $groups = [];
        foreach ($counts as $color => $count) {
            if ($this->findSimilar($color, array_keys($existing)) !== null) {
                continue;
            }
            $representative = $this->findSimilar($color, array_keys($groups));
            $groups[$representative ?? $color] = ($groups[$representative ?? $color] ?? 0) + $count;
        }
        $sort($groups);
        foreach ($groups as $color => $_count) {
            foreach ($pairs as $pair) {
                if (!$pair['optional'] || !empty($values[$pair['background']]) || !empty($values[$pair['contrast']])) {
                    continue;
                }
                $values[$pair['background']] = $color;
                $values[$pair['contrast']] = $this->contrast($color);
                $existing[$color] = true;
                break;
            }
        }
        if ($values !== ($tokens['token'] ?? [])) {
            $tokens['token'] = $values;
        }
        return $tokens;
    }

    /** @param list<string> $colors */
    private function findSimilar(string $color, array $colors): ?string
    {
        $lab = $this->oklab($color);
        foreach ($colors as $candidate) {
            $other = $this->oklab($candidate);
            $distance = sqrt(($lab[0] - $other[0]) ** 2 + ($lab[1] - $other[1]) ** 2 + ($lab[2] - $other[2]) ** 2);
            if ($distance <= self::SIMILARITY_THRESHOLD) {
                return $candidate;
            }
        }
        return null;
    }

    /** @return array{float, float, float} */
    private function oklab(string $hex): array
    {
        $rgb = [];
        foreach ([1, 3, 5] as $offset) {
            $v = hexdec(substr($hex, $offset, 2)) / 255;
            $rgb[] = $v <= 0.04045 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
        }
        [$r, $g, $b] = $rgb;
        $l = (0.4122214708 * $r + 0.5363325363 * $g + 0.0514459929 * $b) ** (1 / 3);
        $m = (0.2119034982 * $r + 0.6806995451 * $g + 0.1073969566 * $b) ** (1 / 3);
        $s = (0.0883024619 * $r + 0.2817188376 * $g + 0.6299787005 * $b) ** (1 / 3);
        return [0.2104542553 * $l + 0.7936177850 * $m - 0.0040720468 * $s,
            1.9779984951 * $l - 2.4285922050 * $m + 0.4505937099 * $s,
            0.0259040371 * $l + 0.7827717662 * $m - 0.8086757660 * $s];
    }

    private function normalize(mixed $color): ?string
    {
        if (!is_string($color)) {
            return null;
        }
        $color = trim($color);
        if (preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $color, $m)) {
            $hex = strtolower($m[1]);
            return '#' . (strlen($hex) === 3 ? $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2] : $hex);
        }
        if (preg_match('/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(1(?:\.0+)?))?\s*\)$/i', $color, $m) && max((int) $m[1], (int) $m[2], (int) $m[3]) <= 255) {
            return sprintf('#%02x%02x%02x', $m[1], $m[2], $m[3]);
        }
        return null;
    }

    private function contrast(string $hex): string
    {
        $channels = [];
        foreach ([1, 3, 5] as $offset) {
            $v = hexdec(substr($hex, $offset, 2)) / 255;
            $channels[] = $v <= 0.04045 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
        }
        $luminance = 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
        return ($luminance + 0.05) / 0.05 >= 1.05 / ($luminance + 0.05) ? '#000000' : '#ffffff';
    }
}
