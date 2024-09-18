<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\SourceInterface;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetTaxonomies;

class PruneTermsNoLongerInUse implements SyncSourceToLocalInterface
{
    public function __construct(
        private SourceInterface $source,
        private GetTaxonomies&GetPostTypeObject $wpService,
        private SyncSourceToLocalInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $this->inner->sync();

        $postTypetaxonomies = get_post_type_object($this->source->getPostType())->taxonomies;

        if (empty($postTypetaxonomies)) {
            return;
        }

        $terms = get_terms([ 'taxonomy' => array_keys($postTypetaxonomies), 'hide_empty' => false, 'count' => true, ]);

        if (empty($terms)) {
            return;
        }

        $emptyTerms = array_filter($terms, fn ($term) => $term->count === 0);

        if (empty($emptyTerms)) {
            return;
        }

        foreach ($emptyTerms as $emptyTerm) {
            wp_delete_term($emptyTerm->term_id, $emptyTerm->taxonomy);
        }
    }
}
