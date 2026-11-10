<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V66;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Completes legacy header colour selections with their contrast pair.
 */
class Version66 implements VersionInterface
{
    private const TOKENS_SETTING = 'tokens';
    private const SURFACE_TOKEN = '--c-header--color--surface';
    private const CONTRAST_TOKEN = '--c-header--color--surface-contrast';

    public function __construct(private GetThemeMod&SetThemeMod $wpService) {}

    public function upgradeToVersion(): void
    {
        $tokens = $this->getStoredTokens();
        $components = $tokens['component'] ?? null;

        if (!is_array($components) || !$this->completeHeaderColorPairs($components)) {
            return;
        }

        $tokens['component'] = $components;
        $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: '');
    }

    /**
     * @param array<string, mixed> $components
     */
    private function completeHeaderColorPairs(array &$components): bool
    {
        $didChange = false;

        foreach ($components as &$componentGroup) {
            if (!is_array($componentGroup) || !is_array($componentGroup['header'] ?? null)) {
                continue;
            }

            $header = &$componentGroup['header'];
            $surface = $header[self::SURFACE_TOKEN] ?? null;
            $contrast = $this->getPairedContrastValue($surface);

            if ($contrast === null || $this->hasValue($header[self::CONTRAST_TOKEN] ?? null)) {
                unset($header);
                continue;
            }

            $header[self::CONTRAST_TOKEN] = $contrast;
            $didChange = true;
            unset($header);
        }
        unset($componentGroup);

        return $didChange;
    }

    private function getPairedContrastValue(mixed $surface): ?string
    {
        if (!is_string($surface)) {
            return null;
        }

        if (preg_match('/^var\\(--color--(primary|secondary|background|surface|palette-(?:[1-9]|10))\\)$/', trim($surface), $matches) !== 1) {
            return null;
        }

        return 'var(--color--' . $matches[1] . '-contrast)';
    }

    private function hasValue(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '';
    }

    /**
     * @return array<string, mixed>
     */
    private function getStoredTokens(): array
    {
        $stored = $this->wpService->getThemeMod(self::TOKENS_SETTING, '');

        if (is_array($stored)) {
            return $stored;
        }

        if (!is_string($stored) || trim($stored) === '') {
            return [];
        }

        $decoded = json_decode($stored, true);
        return is_array($decoded) ? $decoded : [];
    }
}
