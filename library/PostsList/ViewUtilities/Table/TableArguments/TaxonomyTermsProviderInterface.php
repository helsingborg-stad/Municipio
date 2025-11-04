<?php

namespace Municipio\PostsList\ViewUtilities\Table\TableArguments;

use WP_Term;

interface TaxonomyTermsProviderInterface
{
    /**
     * Get all terms associated with the posts
     *
     * @return WP_Term[]
     */
    public function getAllTerms(): array;
}
