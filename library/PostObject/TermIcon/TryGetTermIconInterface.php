<?php

namespace Municipio\PostObject\TermIcon;

use Municipio\PostObject\TermIcon\TermIconInterface;

interface TryGetTermIconInterface
{
    /**
     * Tries to get the term icon for a given term ID and taxonomy.
     *
     * @param int $termId The term ID.
     * @param string $taxonomy The taxonomy.
     * @return TermIconInterface|null The term icon or null if not found.
     */
    public function tryGetTermIcon(int $termId, string $taxonomy): ?TermIconInterface;
}
