<?php

declare(strict_types=1);

namespace Modularity\Helper;

/**
 * Query vars that belong to one module, such as its pagination: mod-posts-123-page.
 *
 * Only these may vary a module's fragment cache. Other query vars are set by the
 * visitor, and letting them into the cache key would add a new entry per request.
 */
final class ModuleQueryVars
{
    public function __construct(
        private readonly string $prefix,
    ) {}

    public static function fromModule(\WP_Post $module): self
    {
        return new self($module->post_type . '-' . $module->ID . '-');
    }

    /**
     * @param array<array-key, mixed> $queryParams For example $_GET
     * @return array<string, string>
     */
    public function extract(array $queryParams): array
    {
        return array_filter(
            $queryParams,
            fn(mixed $value, int|string $name): bool => is_string($value) && $this->belongsToModule((string) $name),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    private function belongsToModule(string $name): bool
    {
        return str_starts_with($name, $this->prefix);
    }
}
