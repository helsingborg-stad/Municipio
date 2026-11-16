<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders\Filter\TermPostCounts;

use WP_Term;

/**
 * Decides which post count each taxonomy filter term shows, and which terms are shown at all.
 */
interface TermPostCountsInterface
{
    /**
     * Apply post counts to the terms of a taxonomy.
     *
     * @param string $taxonomy
     * @param WP_Term[] $terms
     * @param string[] $selectedTermSlugs Slugs of the terms currently filtered on.
     * @return WP_Term[] The terms to show, with `count` set to the post count to display.
     */
    public function applyToTerms(string $taxonomy, array $terms, array $selectedTermSlugs): array;
}
