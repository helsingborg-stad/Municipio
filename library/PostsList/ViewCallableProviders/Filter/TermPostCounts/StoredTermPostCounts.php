<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders\Filter\TermPostCounts;

/**
 * Shows all terms with the post count stored on the term, i.e. all published posts.
 */
class StoredTermPostCounts implements TermPostCountsInterface
{
    /**
     * @inheritDoc
     */
    public function applyToTerms(string $taxonomy, array $terms, array $selectedTermSlugs): array
    {
        return $terms;
    }
}
