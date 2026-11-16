<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders\Filter\TermPostCounts;

/**
 * Resolves how many posts in a posts list each term of a taxonomy matches.
 */
interface TermPostCountsInterface
{
    /**
     * Get the number of posts per term for a taxonomy.
     *
     * @param string $taxonomy
     * @return array<int, int> Post count keyed by term ID. Terms without matching posts are left out.
     */
    public function getCounts(string $taxonomy): array;
}
