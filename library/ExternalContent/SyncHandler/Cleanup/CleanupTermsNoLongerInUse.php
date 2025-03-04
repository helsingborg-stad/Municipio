<?php

namespace Municipio\ExternalContent\SyncHandler\Cleanup;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\SyncHandler\SyncHandler;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\{AddAction, GetTerms, WpDeleteTerm};

/**
 * Class CleanupTermsNoLongerInUse
 *
 * This class is responsible for cleaning up terms that are no longer in use.
 */
class CleanupTermsNoLongerInUse implements Hookable
{
    /**
     * Constructor for PruneTermsNoLongerInUse.
     */
    public function __construct(
        private SourceConfigInterface $sourceConfig,
        private AddAction&GetTerms&WpDeleteTerm $wpService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction(SyncHandler::ACTION_AFTER, [$this, 'cleanup']);
    }

    /**
     * Cleanup terms that are no longer in use.
     */
    public function cleanup(): void
    {
        if (empty($this->sourceConfig->getTaxonomies())) {
            return;
        }

        $taxonomyNames = array_map(fn($taxonomy) =>  $taxonomy->getName(), $this->sourceConfig->getTaxonomies());

        $terms = $this->wpService->getTerms([
            'taxonomy'   => $taxonomyNames,
            'hide_empty' => false,
            'count'      => true,
        ]);

        if (empty($terms)) {
            return;
        }

        $emptyTerms = array_filter($terms, fn ($term) => $term->count === 0);

        if (empty($emptyTerms)) {
            return;
        }

        foreach ($emptyTerms as $emptyTerm) {
            $this->wpService->wpDeleteTerm($emptyTerm->term_id, $emptyTerm->taxonomy);
        }
    }
}
