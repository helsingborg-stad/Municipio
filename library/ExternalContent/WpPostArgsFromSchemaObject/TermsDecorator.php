<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Taxonomy\TaxonomyItemInterface;
use Municipio\ExternalContent\WpTermFactory\WpTermFactoryInterface;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\PropertyValue;
use WP_Term;
use WpService\Contracts\TermExists;
use WpService\Contracts\WpInsertTerm;

/**
 * Class WpPostMetaFactoryVersionDecorator
 */
class TermsDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * WpPostMetaFactoryVersionDecorator constructor.
     *
     * @param TaxonomyItemInterface[] $taxonomyItems
     * @param WpPostMetaFactoryInterface $inner
     */
    public function __construct(
        private array $taxonomyItems,
        private WpTermFactoryInterface $wpTermFactory,
        private WpInsertTerm&TermExists $wpService,
        private WpPostArgsFromSchemaObjectInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(BaseType $schemaObject): array
    {
        $post                  = $this->inner->create($schemaObject);
        $matchingTaxonomyItems = $this->tryGetMatchingTaxonomyItems($schemaObject);

        if (!isset($post['tax_input'])) {
            $post['tax_input'] = [];
        }

        foreach ($matchingTaxonomyItems as $taxonomyItem) {
            $termNames = $this->getTermNamesFromSchemaProperty($schemaObject, $taxonomyItem);
            $wpTerms   = array_map(fn ($term) => $this->wpTermFactory->create(
                $term,
                $taxonomyItem->getName()
            ), $termNames);
            $termIds   = $this->getTermIdsFromTerms($wpTerms, $taxonomyItem->getName());

            $post['tax_input'][$taxonomyItem->getName()] = $termIds;
        }

        return $post;
    }

    /**
     * Get term names from schema property.
     *
     * @param BaseType $schemaObject
     * @param TaxonomyItemInterface $taxonomyItem
     * @return string[]
     */
    private function getTermNamesFromSchemaProperty(
        BaseType $schemaObject,
        TaxonomyItemInterface $taxonomyItem
    ): array {
        $results = [];
        $value   = $this->getSchemaObjectPropertyValueByPropertyPath(
            $schemaObject,
            $taxonomyItem->getSchemaObjectProperty()
        );

        array_walk_recursive($value, function ($item) use (&$results) {
            $results[] = $item;
        });

        return array_filter($results);
    }

    /**
     * Get property value by property path.
     *
     * @param BaseType $schemaObject
     * @param string $propertyPath Name of a property or path to a nested property. Example: 'name', 'address.street'
     * @return string[]|null[]
     */
    private function getSchemaObjectPropertyValueByPropertyPath(mixed $schemaObject, string $propertyPath): array
    {
        $propertyPathParts = explode('.', $propertyPath);
        $propertyValue     = $schemaObject;

        if (count($propertyPathParts) === 1 && is_array($propertyValue)) {
            return array_map(
                fn ($value) => $this->getSchemaObjectPropertyValueByPropertyPath($value, $propertyPath),
                $propertyValue
            );
        } elseif (count($propertyPathParts) === 1) {
            if ($propertyValue instanceof PropertyValue && $propertyValue->getProperty('name') === $propertyPath) {
                $propertyValue = $propertyValue->getProperty('value');
            } elseif ($propertyValue instanceof BaseType) {
                $propertyValue = $propertyValue->getProperty($propertyPath);
            }
        } else {
            return $this->getSchemaObjectPropertyValueByPropertyPath(
                $propertyValue->getProperty($propertyPathParts[0]),
                implode('.', array_slice($propertyPathParts, 1))
            );
        }

        if (is_array($propertyValue)) {
            return array_map(fn ($value) => $this->convertPropertyValueToTermNames($value), $propertyValue);
        }

        return [$this->convertPropertyValueToTermNames($propertyValue)];
    }

    /**
     * Convert property value to term names.
     *
     * @param mixed $value
     * @return string|null Term name. Null if value is not a string or PropertyValue.
     */
    private function convertPropertyValueToTermNames(mixed $value): ?string
    {
        return match (true) {
            is_string($value) => $value,
            $value instanceof PropertyValue => $value->getProperty('value'),
            $value instanceof BaseType => $value->getProperty('name'),
            default => null,
        };
    }

    /**
     * Get matching taxonomy items.
     *
     * @param BaseType $schemaObject
     * @return TaxonomyItemInterface[]
     */
    private function tryGetMatchingTaxonomyItems(BaseType $schemaObject): array
    {
        return array_filter(
            $this->taxonomyItems,
            fn($taxonomyItem) =>
                $taxonomyItem->getSchemaObjectType() === $schemaObject->getType() &&
            $schemaObject->hasProperty($taxonomyItem->getSchemaObjectProperty())
        );
    }

    /**
     * Get term ids from terms.
     * If term does not exist, it will be created.
     *
     * @param WP_Term[] $terms
     * @param string $taxonomy
     * @return int[]
     */
    private function getTermIdsFromTerms(array $terms, string $taxonomy): array
    {
        $termIds = array_map(function ($term) use ($taxonomy) {

            $termExists = $this->wpService->termExists($term->name, $taxonomy);

            if (!empty($termExists) && is_array($termExists) && isset($termExists['term_id'])) {
                return $termExists['term_id'];
            }

            $insertedTerm = $this->wpService->wpInsertTerm($term->name, $taxonomy);

            if (is_array($insertedTerm) && isset($insertedTerm['term_id'])) {
                return $insertedTerm['term_id'];
            }

            return null;
        }, $terms);

        return array_filter(array_map('intval', $termIds));
    }
}
