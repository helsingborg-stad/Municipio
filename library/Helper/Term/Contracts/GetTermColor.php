<?php

namespace Municipio\Helper\Term\Contracts;

interface GetTermColor
{
    /**
     * Get the color associated with a term.
     *
     * @param int $termId The term to get the colour for. Can be a term object, term ID or term slug.
     * @return string The color associated with the term or false if no color is set.
     */
    public function getTermColor(int|string|\WP_Term $term, string $taxonomy = ''): false|string;
}
