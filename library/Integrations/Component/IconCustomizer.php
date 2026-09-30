<?php

declare(strict_types=1);

namespace Municipio\Integrations\Component;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Applies the global icon settings to Component Library icon instances.
 */
class IconCustomizer implements Hookable
{
    private const VARIANTS = ['outlined', 'rounded', 'sharp'];
    private const WEIGHTS = [200, 400, 600];

    public function __construct(private WpService $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            'ComponentLibrary/Component/Icon/Data',
            [$this, 'applyIconSettings'],
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function applyIconSettings(array $data): array
    {
        $variant = $this->wpService->getThemeMod('icon_style');
        $weight = $this->wpService->getThemeMod('icon_weight');

        $data['variant'] = is_string($variant) && in_array($variant, self::VARIANTS, true)
            ? $variant
            : 'outlined';
        $data['weight'] = in_array((int) $weight, self::WEIGHTS, true)
            ? (int) $weight
            : 400;

        return $data;
    }
}
