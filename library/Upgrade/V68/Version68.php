<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V68;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Removes legacy computed footer colour overrides.
 */
class Version68 implements VersionInterface
{
    private const TOKENS_SETTING = 'tokens';

    /** @var list<string> */
    private const LEGACY_COLOR_TOKENS = [
        '--c-footer--background-color',
        '--c-footer--color',
        '--c-footer--border-color',
        '--c-footer--subfooter-background-color',
        '--c-footer--subfooter-color',
        '--c-footer--subfooter-separator-color',
    ];

    public function __construct(private GetThemeMod&SetThemeMod $wpService) {}

    public function upgradeToVersion(): void
    {
        $tokens = $this->getStoredTokens();
        $components = $tokens['component'] ?? null;

        if (!is_array($components) || !$this->removeLegacyFooterColorTokens($components)) {
            return;
        }

        $tokens['component'] = $components;
        $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: '');
    }

    /**
     * @param array<string, mixed> $components
     */
    private function removeLegacyFooterColorTokens(array &$components): bool
    {
        $didChange = false;

        foreach ($components as &$componentGroup) {
            if (!is_array($componentGroup) || !is_array($componentGroup['footer'] ?? null)) {
                continue;
            }

            $footer = &$componentGroup['footer'];

            foreach (self::LEGACY_COLOR_TOKENS as $token) {
                if (!array_key_exists($token, $footer)) {
                    continue;
                }

                unset($footer[$token]);
                $didChange = true;
            }

            unset($footer);
        }
        unset($componentGroup);

        return $didChange;
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
