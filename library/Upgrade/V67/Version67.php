<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V67;

use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Removes legacy computed header colour overrides.
 */
class Version67 implements VersionInterface
{
    private const TOKENS_SETTING = 'tokens';

    /** @var list<string> */
    private const LEGACY_COLOR_TOKENS = [
        '--c-header--background-color',
        '--c-header--color',
        '--c-header--border-color',
    ];

    public function __construct(private GetThemeMod&SetThemeMod $wpService) {}

    public function upgradeToVersion(): void
    {
        $tokens = $this->getStoredTokens();
        $components = $tokens['component'] ?? null;

        if (!is_array($components) || !$this->removeLegacyHeaderColorTokens($components)) {
            return;
        }

        $tokens['component'] = $components;
        $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: '');
    }

    /**
     * @param array<string, mixed> $components
     */
    private function removeLegacyHeaderColorTokens(array &$components): bool
    {
        $didChange = false;

        foreach ($components as &$componentGroup) {
            if (!is_array($componentGroup) || !is_array($componentGroup['header'] ?? null)) {
                continue;
            }

            $header = &$componentGroup['header'];

            foreach (self::LEGACY_COLOR_TOKENS as $token) {
                if (!array_key_exists($token, $header)) {
                    continue;
                }

                unset($header[$token]);
                $didChange = true;
            }

            unset($header);
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
