<?php

namespace Municipio\Helper\Term\Contracts;

interface CreateOrGetTermIdFromString
{
    /**
     * Create or get a term from a string.
     *
     * @param string $termString The term string.
     * @param string $taxonomy The taxonomy.
     * @return int|null The term ID or null if the term could not be created or found.
     */
    public function createOrGetTermIdFromString(string $termString, string $taxonomy): ?int;
}
