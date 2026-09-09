<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

/**
 * Decorates a taxonomy so that only terms originating from schema items matching an
 * expected value on a given sub-property are kept.
 *
 * Example: create terms from `keywords` only when the keyword's `inDefinedTermSet.name`
 * equals `event_status`, where `keywords` is an array of `DefinedTerm` schema objects.
 *
 * Since the taxonomy name is normally derived from the schema type and property alone, decorating
 * the same inner taxonomy more than once (e.g. to split `keywords` into several taxonomies by
 * different `inDefinedTermSet.name` values) would otherwise produce colliding taxonomy names.
 * The name is therefore made unique per expected value unless an explicit name is provided.
 */
class TaxonomyFilteredBySubProperty implements TaxonomyInterface
{
    private const MAX_TAXONOMY_NAME_LENGTH = 32;

    /**
     * @param TaxonomyInterface $inner The decorated taxonomy.
     * @param string $itemsPropertyPath Dot-notated path to the array of raw schema items to inspect, e.g. 'keywords'.
     * @param string $subPropertyPath Dot-notated path, relative to each item, to the sub-property to match, e.g. 'inDefinedTermSet.name'.
     * @param string|string[] $expectedValue Value(s) the sub-property must match for the item to be kept.
     * @param string|null $name Explicit taxonomy name. When omitted, a name unique to $expectedValue is derived from the inner taxonomy's name.
     */
    public function __construct(
        private TaxonomyInterface $inner,
        private string $itemsPropertyPath,
        private string $subPropertyPath,
        private string|array $expectedValue,
        private ?string $name = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name ?? $this->getDerivedName();
    }

    /**
     * Derive a taxonomy name that is unique per expected value, so multiple decorators wrapping the
     * same inner taxonomy (same schema type/property) don't collide on name.
     *
     * @return string The derived, WordPress-safe taxonomy name.
     */
    private function getDerivedName(): string
    {
        $safeExpected = $this->getNameSafeExpectedValue();
        $suffix = '_' . substr($safeExpected, 0, self::MAX_TAXONOMY_NAME_LENGTH - 1);
        $prefixLength = max(0, self::MAX_TAXONOMY_NAME_LENGTH - strlen($suffix));

        return substr($this->inner->getName(), 0, $prefixLength) . $suffix;
    }

    /**
     * Turn the expected value(s) into a string that is safe to use in a taxonomy name.
     *
     * @return string
     */
    private function getNameSafeExpectedValue(): string
    {
        $value = is_array($this->expectedValue) ? implode('_', $this->expectedValue) : $this->expectedValue;

        return strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '_', $value));
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

        $allowedNames = $this->getAllowedNamesFromSchema($schema);

        if (is_array($formatted)) {
            $filtered = array_values(array_intersect($formatted, $allowedNames));
            return empty($filtered) ? null : $filtered;
        }

        return in_array($formatted, $allowedNames, true) ? $formatted : null;
    }

    /**
     * Collect the `name` of every raw schema item whose sub-property matches the expected value.
     *
     * @param array $schema The full schema data.
     * @return string[] The allowed term names.
     */
    private function getAllowedNamesFromSchema(array $schema): array
    {
        $items = $this->extractValueByPath($schema, $this->itemsPropertyPath);

        if ($items === null) {
            return [];
        }

        // Normalize a single item into a list of one, so both shapes can be handled the same way.
        $items = is_array($items) && array_is_list($items) ? $items : [$items];

        $allowedNames = [];
        foreach ($items as $item) {
            if (!is_array($item) || !isset($item['name'])) {
                continue;
            }

            if ($this->matchesExpectedValue($this->extractValueByPath($item, $this->subPropertyPath))) {
                $allowedNames[] = $item['name'];
            }
        }

        return $allowedNames;
    }

    /**
     * Extract a value from an array using a dot-notated path.
     *
     * @param array $data The data to extract from.
     * @param string $path The dot-notated path, e.g. 'inDefinedTermSet.name'.
     * @return mixed The extracted value, or null if the path does not exist.
     */
    private function extractValueByPath(array $data, string $path): mixed
    {
        $value = $data;

        foreach (explode('.', $path) as $segment) {
            if (!is_array($value) || !isset($value[$segment])) {
                return null;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Check if a sub-property value matches one of the expected values.
     *
     * @param mixed $subValue The value extracted from the sub-property path.
     * @return bool True if it matches, false otherwise.
     */
    private function matchesExpectedValue(mixed $subValue): bool
    {
        $expectedValues = is_array($this->expectedValue) ? $this->expectedValue : [$this->expectedValue];
        return in_array($subValue, $expectedValues, true);
    }
}
