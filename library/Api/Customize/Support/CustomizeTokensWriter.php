<?php

declare(strict_types=1);

namespace Municipio\Api\Customize\Support;

use Municipio\Api\Customize\Config\CustomizeConfigInterface;
use WP_REST_Request;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\UpdatePostMeta;
use WpService\Contracts\WpSavePostRevision;

class CustomizeTokensWriter implements CustomizeTokensWriterInterface
{
    public function __construct(
        private readonly SetThemeMod&UpdatePostMeta&WpSavePostRevision $wpService,
        private readonly CustomizeConfigInterface $config,
        private readonly ChangesetIdResolverInterface $changesetIdResolver,
    ) {}

    /**
     * Persist raw JSON customization payload for current request context.
     */
    public function write(WP_REST_Request $request, string $encodedTokens): bool
    {
        $changesetId = $this->changesetIdResolver->resolve($request);
        if ($changesetId !== null) {
            $didSave = $this->wpService->updatePostMeta(
                $changesetId,
                $this->config->getThemeModKey(),
                $encodedTokens,
            );

            if ($didSave !== false) {
                $this->wpService->wpSavePostRevision($changesetId);
                return true;
            }

            return false;
        }

        return $this->wpService->setThemeMod($this->config->getThemeModKey(), $encodedTokens);
    }
}
