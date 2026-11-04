<?php

declare(strict_types=1);

namespace Municipio\ImagePreload;

use ComponentLibrary\Integrations\Image\ImageInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/** Registers the LQIPs used by rendered Image components for head preloading. */
class LqipPreloadCollector implements Hookable
{
    public function __construct(
        private AddFilter $wpService,
        private LqipPreloadConfigInterface $config,
    )
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            'ComponentLibrary/Component/Image/Data',
            [$this, 'collect'],
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function collect(array $data): array
    {
        $source = $data['src'] ?? null;
        $lqipEnabled = $data['lqipEnabled'] ?? true;

        if (!$this->config->isEnabled() || $lqipEnabled !== true || !$source instanceof ImageInterface) {
            return $data;
        }

        $lqipUrl = $source->getLqipUrl();
        if (is_string($lqipUrl)) {
            LqipPreloadRegistry::add($lqipUrl);
        }

        return $data;
    }
}
