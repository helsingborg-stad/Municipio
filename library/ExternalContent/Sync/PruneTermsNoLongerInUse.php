<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use WpService\Contracts\WpDeleteTerm;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetTaxonomies;
use WpService\Contracts\GetTerms;

/**
 * Class PruneTermsNoLongerInUse
 *
 * This class is responsible for pruning terms that are no longer in use.
 */
class PruneTermsNoLongerInUse implements SyncSourceToLocalInterface
{
    /**
     * Constructor for PruneTermsNoLongerInUse.
     */
    public function __construct(
        private SourceInterface $source,
        private GetTaxonomies&GetPostTypeObject&GetTerms&WpDeleteTerm $wpService,
        private SyncSourceToLocalInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $this->inner->sync();

        $postTypetaxonomies = $this->wpService->getPostTypeObject($this->source->getPostType())->taxonomies;

        if (empty($postTypetaxonomies)) {
            return;
        }

        $terms = $this->wpService->getTerms([
            'taxonomy'   => array_keys($postTypetaxonomies),
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
