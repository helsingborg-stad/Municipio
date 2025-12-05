<?php

namespace Municipio\PostsList\ViewCallableProviders\Table\TableArguments;

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
