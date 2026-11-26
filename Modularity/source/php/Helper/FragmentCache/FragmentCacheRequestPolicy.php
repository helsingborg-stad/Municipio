<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

/**
 * Decides whether a request may read and write the fragment cache.
 *
 * Only requests without parameters that may change module markup use the cache.
 * Module parameters (mod-*) and registered query vars are set by the visitor and
 * can also end up in links, such as pagination. Keeping them out of the cache keeps
 * the cache bounded and stops one visitor's parameters from being served to others.
 * Requests with such parameters render without the cache.
 */
final class FragmentCacheRequestPolicy
{
    private const MODULE_PARAMETER_PREFIX = 'mod-';

    /**
     * @param array<array-key, mixed> $queryParams          The query string parameters, for example $_GET
     * @param list<string>            $registeredQueryVars  Query vars registered with WordPress
     */
    public function __construct(
        private readonly array $queryParams,
        private readonly array $registeredQueryVars,
    ) {}

    /**
     * Creates the policy for the current request.
     */
    public static function fromCurrentRequest(): self
    {
        // @mago-expect lint:no-global
        global $wp;

        return new self($_GET, (array) ($wp->public_query_vars ?? []));
    }

    public function allowsCaching(): bool
    {
        foreach (array_keys($this->queryParams) as $name) {
            if ($this->mayChangeMarkup((string) $name)) {
                return false;
            }
        }

        return true;
    }

    private function mayChangeMarkup(string $name): bool
    {
        return stripos($name, self::MODULE_PARAMETER_PREFIX) === 0 || in_array($name, $this->registeredQueryVars, true);
    }
}
