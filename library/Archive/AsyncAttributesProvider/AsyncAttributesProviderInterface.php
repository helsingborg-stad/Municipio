<?php

declare(strict_types=1);

namespace Municipio\Archive\AsyncAttributesProvider;

/**
 * Interface for providing async attributes for archive pages
 *
 * Implementations of this interface should provide JSON-serializable
 * attributes that can be used for async rendering and client-side hydration.
 */
interface AsyncAttributesProviderInterface
{
    /**
     * Get async attributes
     *
     * Returns an array of attributes that will be JSON-encoded and added
     * to the data-posts-list-attributes attribute in the rendered output.
     *
     * @return array Array of JSON-serializable attributes
     */
    public function getAttributes(): array;
}
