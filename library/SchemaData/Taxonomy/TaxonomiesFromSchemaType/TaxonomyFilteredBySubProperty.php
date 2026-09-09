<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

/**
 * Decorates a taxonomy so that only terms originating from schema items matching an
 * expected value on a given sub-property are kept.
 *
 * Example: create terms from `keywords` only when the keyword's `inDefinedTermSet.name`
 * equals `event_status`, where `keywords` is an array of `DefinedTerm` schema objects.
 */
class TaxonomyFilteredBySubProperty implements TaxonomyInterface
{
    /**
     * @param TaxonomyInterface $inner The decorated taxonomy.
     * @param string $itemsPropertyPath Dot-notated path to the array of raw schema items to inspect, e.g. 'keywords'.
     * @param string $subPropertyPath Dot-notated path, relative to each item, to the sub-property to match, e.g. 'inDefinedTermSet.name'.
     * @param string|string[] $expectedValue Value(s) the sub-property must match for the item to be kept.
     */
    public function __construct(
        private TaxonomyInterface $inner,
        private string $itemsPropertyPath,
        private string $subPropertyPath,
        private string|array $expectedValue,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->inner->getName();
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
