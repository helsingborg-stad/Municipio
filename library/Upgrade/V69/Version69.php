<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V69;

use Municipio\Upgrade\ComponentColorPairs;
use Municipio\Upgrade\VersionInterface;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/** Completes persisted component colour pairs once, before rendering them. */
class Version69 implements VersionInterface
{
    public function __construct(
        private GetThemeMod&SetThemeMod $wpService,
    ) {}

    public function upgradeToVersion(): void
    {
        $stored = $this->wpService->getThemeMod('tokens', '');
        $tokens = is_string($stored) ? json_decode($stored, true) : $stored;
        if (!is_array($tokens) || !is_array($tokens['component'] ?? null)) {
            return;
        }

        $originalComponents = $tokens['component'];
        foreach ($tokens['component'] as &$componentGroup) {
            if (!is_array($componentGroup)) {
                continue;
            }
            foreach (['header', 'footer'] as $component) {
                if (!is_array($componentGroup[$component] ?? null)) {
                    continue;
                }
                $componentGroup[$component] = (new ComponentColorPairs())->migrate($component, $componentGroup[$component]);
            }
        }
        unset($componentGroup);

        if ($tokens['component'] !== $originalComponents) {
            $this->wpService->setThemeMod('tokens', json_encode($tokens, JSON_THROW_ON_ERROR));
        }
    }
}
