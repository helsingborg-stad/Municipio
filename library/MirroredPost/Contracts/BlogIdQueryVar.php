<?php

namespace Municipio\MirroredPost\Contracts;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * BlogIdQueryVar
 *
 * This class defines a constant for the query variable used to fetch posts from a specific blog in a multisite setup.
 */
final class BlogIdQueryVar implements Hookable
{
    public const BLOG_ID_QUERY_VAR = 'fetch_from_blog';

    /**
     * Constructor for the BlogIdQueryVar class.
     *
     * @param AddFilter $wpS An instance of AddFilter used to register or handle WordPress filters.
     */
    public function __construct(private AddFilter $wpService)
    {
    }

    /**
     * Append the blog ID query variable to the list of query variables.
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('query_vars', [$this, 'appendToQueryVars']);
    }

    /**
     * Append the blog ID query variable to the list of query variables.
     *
     * @param array $queryVars The existing query variables.
     * @return array The updated query variables with the blog ID query variable appended.
     */
    public function appendToQueryVars(array $queryVars): array
    {
        return [...$queryVars, self::BLOG_ID_QUERY_VAR];
    }
}
