<?php

declare(strict_types=1);

namespace Municipio\Api\Customize\Support;

use Municipio\Api\Customize\Config\CustomizeConfigInterface;
use WP_REST_Request;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetThemeMod;

class CustomizeTokensReader implements CustomizeTokensReaderInterface
{
    public function __construct(
        private readonly GetThemeMod&GetPostMeta $wpService,
        private readonly CustomizeConfigInterface $config,
        private readonly ChangesetIdResolverInterface $changesetIdResolver,
    ) {}

    /**
     * Read raw JSON customization payload for current request context.
     */
    public function read(WP_REST_Request $request): ?string
    {
        $changesetId = $this->changesetIdResolver->resolve($request);
        if ($changesetId !== null) {
            $changesetCustomizations = $this->wpService->getPostMeta(
                $changesetId,
                $this->config->getThemeModKey(),
                true,
            );

            if (is_string($changesetCustomizations)) {
                return $changesetCustomizations;
            }
        }

        $customizations = $this->wpService->getThemeMod($this->config->getThemeModKey());
        return is_string($customizations) ? $customizations : null;
    }
}
