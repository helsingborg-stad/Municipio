<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

/**
 * Decorates a taxonomy so that term values are translated through a fixed map before terms are created.
 * Values missing from the map are dropped.
 *
 * Example: turn the boolean `isAccessibleForFree` (sanitized to 'true'/'false') into a single
 * human readable term, e.g. ['true' => 'Free'], so events can be filtered on being free.
 */
class TaxonomyWithMappedTermValues implements TaxonomyInterface
{
    /**
     * @param TaxonomyInterface $inner The decorated taxonomy.
     * @param array<string, string> $valueMap Map from formatted term value to the term name to use.
     * @param string|null $name Explicit taxonomy name. When omitted, the inner taxonomy's name is used.
     */
    public function __construct(
        private TaxonomyInterface $inner,
        private array $valueMap,
        private ?string $name = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name ?? $this->inner->getName();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaType(): string
    {
        return $this->inner->getSchemaType();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(): string
    {
        return $this->inner->getSchemaProperty();
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypes(): array
    {
        return $this->inner->getObjectTypes();
    }

    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        return $this->inner->getArguments();
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->inner->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function getSingularLabel(): string
    {
        return $this->inner->getSingularLabel();
    }

    /**
     * @inheritDoc
     */
    public function formatTermValue(mixed $value, array $schema): string|array|null
    {
        $formatted = $this->inner->formatTermValue($value, $schema);

        if ($formatted === null) {
            return null;
        }

        if (is_array($formatted)) {
            $mapped = array_values(array_filter(array_map(fn($item) => $this->mapValue($item), $formatted)));
            return empty($mapped) ? null : $mapped;
        }

        return $this->mapValue($formatted);
    }

    /**
     * Look up a single value in the value map.
     *
     * @param mixed $value The formatted value.
     * @return string|null The mapped term name, or null if the value is not mapped.
     */
    private function mapValue(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        return $this->valueMap[(string) $value] ?? null;
    }
}
