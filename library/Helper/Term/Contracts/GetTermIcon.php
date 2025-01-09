<?php

namespace Municipio\Helper\Term\Contracts;

interface GetTermIcon
{
    /**
     * Get the icon for a given term.
     *
     * @param int $termId * @param mixed $term The term to retrieve the icon for. Can be a WP_Term object, ID, or slug.
     * @param string $taxonomy The taxonomy of the term. (not needed if $term is a WP_Term object)
     * @return array|false The icon of the term and the icon type or false if it can't be found.
     */
    public function getTermIcon(int|string|\WP_Term $term, string $taxonomy = ''): array|false;
}
