<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomyInterface;
use WP_Term;

/**
 * Class TermFactory
 *
 * This class is responsible for creating WP_Term objects based on the provided taxonomy and schema data.
 * It extracts the relevant property values from the schema and returns them as an array of term names.
 */
class TermFactory implements TermFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(TaxonomyInterface $taxonomy, array $schema): array
    {
        $propertyPath = explode('.', $taxonomy->getSchemaProperty());
        $values       = $this->extractPropertyValue($schema, $propertyPath);

        if (empty($values)) {
            return [];
        }

        return $this->createTermsFromValues(array_filter(is_array($values) ? $values : [$values]), $taxonomy, $schema);
    }

    /**
     * Create WP_Term objects from an array of values.
     *
     * @param array $values An array of term values.
     * @param TaxonomyInterface $taxonomy The taxonomy to create terms for.
     *
     * @return WP_Term[] An array of WP_Term objects.
     */
    private function createTermsFromValues(array $values, TaxonomyInterface $taxonomy, array $schema = []): array
    {
        $terms = [];
        foreach ($values as $value) {
            $formatted = $taxonomy->formatTermValue($value, $schema);
            if (is_array($formatted)) {
                foreach ($formatted as $f) {
                    $terms[] = $this->makeTerm($f, $taxonomy);
                }
            } elseif ($formatted !== null) {
                $terms[] = $this->makeTerm($formatted, $taxonomy);
            }
        }
        return $terms;
    }

    /**
     * Create a WP_Term object from a value.
     *
     * @param mixed $value The value to create the term from.
     * @param TaxonomyInterface $taxonomy The taxonomy the term belongs to.
     *
     * @return WP_Term The created WP_Term object.
     */
    private function makeTerm($value, TaxonomyInterface $taxonomy): WP_Term
    {
        $term           = new WP_Term(new \stdClass());
        $term->name     = $value;
        $term->taxonomy = $taxonomy->getName();
        return $term;
    }

    /**
     * Recursively extract the value from the schema data based on the property path.
     *
     * @param array $data         The schema data.
     * @param array $propertyPath The property path to extract.
     *
     * @return mixed The extracted value, sanitized.
     */
    private function extractPropertyValue(array $data, array $propertyPath): mixed
    {
        $currentKey = array_shift($propertyPath);

        if (isset($data[$currentKey])) {
            $value = $data[$currentKey];

            if (empty($propertyPath)) {
                return $this->sanitizeValue($value);
            }

            if (is_array($value)) {
                return $this->extractPropertyValue($value, $propertyPath);
            }
        }

        // Handle arrays of objects (e.g., [ ['foo' => ...], ... ])
        if (isset($data[0][$currentKey])) {
            return array_map(
                fn($item) => $this->extractPropertyValue($item, [...$propertyPath, $currentKey]),
                $data
            );
        } elseif ($matchingSetOfPropertyValues = $this->tryToGetMatchingSetOfPropertyValues($data, $currentKey)) {
            return $matchingSetOfPropertyValues;
        }

        return [];
    }

    /**
     * Try to get a matching set of PropertyValue objects from the data.
     *
     * @param mixed  $data               The data to search.
     * @param string $propertyValueName  The name of the PropertyValue to match.
     *
     * @return array|null An array of matching values, or null if none found.
     */
    private function tryToGetMatchingSetOfPropertyValues(mixed $data, string $propertyValueName): ?array
    {
        if (!is_array($data)) {
            return null;
        }

        $matches = array_filter($data, function ($item) use ($propertyValueName) {
            return
                isset($item['@type']) && $item['@type'] === 'PropertyValue' &&
                isset($item['name']) && $item['name'] === $propertyValueName &&
                isset($item['value']) && is_string($item['value']);
        });

        return !empty($matches) ? array_map(fn($item) => $item['value'], $matches) : null;
    }

    /**
     * Sanitize the value to ensure it is a string or an array of strings.
     *
     * @param mixed $value The value to sanitize.
     *
     * @return string|array|null The sanitized value.
     */
    private function sanitizeValue(mixed $value): string|array|null
    {
        if (is_int($value) || is_float($value)) {
            return (string)$value;
        }

        if (is_string($value)) {
            return $value;
        }

        if ($this->isSchemaWithName($value)) {
            return $value['name'];
        }

        if (is_array($value)) {
            return array_filter(
                array_map(fn($item) => $this->sanitizeValue($item), $value),
                fn($item) => $item !== null
            );
        }

        return null;
    }

    /**
     * Check if the value is a schema object with a name property.
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value is a schema object with a name property, false otherwise.
     */
    private function isSchemaWithName(mixed $value): bool
    {
        return is_array($value) && isset($value['@type'], $value['name']);
    }
}
