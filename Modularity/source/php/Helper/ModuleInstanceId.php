<?php

declare(strict_types=1);

namespace Modularity\Helper;

/**
 * Wrapper id for one rendered placement of a module.
 *
 * Markup is rendered with a placeholder instead of a real id, so it can be
 * fragment-cached under a key that is stable between requests. The placeholder
 * is replaced with a unique id each time the markup is output.
 */
final class ModuleInstanceId
{
    private const PLACEHOLDER_SUFFIX = '{modularity-instance-id}';

    public function __construct(
        private readonly string $prefix,
    ) {}

    public static function fromModule(\WP_Post $module): self
    {
        return new self($module->post_type . '-' . $module->ID . '-');
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getPlaceholder(): string
    {
        return $this->prefix . self::PLACEHOLDER_SUFFIX;
    }

    public function replacePlaceholder(string $markup, string $instanceId): string
    {
        return str_replace($this->getPlaceholder(), $instanceId, $markup);
    }
}
