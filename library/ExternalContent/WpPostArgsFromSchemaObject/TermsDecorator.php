<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\Taxonomy\TaxonomyItemInterface;
use Municipio\ExternalContent\Taxonomy\TaxonomyRegistrarInterface;
use Municipio\ExternalContent\WpTermFactory\WpTermFactoryInterface;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Contracts\PropertyValueContract;
use Spatie\SchemaOrg\PropertyValue;
use WP_Term;
use WpService\Contracts\InsertTerm;
use WpService\Contracts\TermExists;

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
        private InsertTerm&TermExists $wpService,
        private WpPostArgsFromSchemaObjectInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $post                  = $this->inner->create($schemaObject, $source);
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

    private function getTermNamesFromSchemaProperty(
        BaseType $schemaObject,
        TaxonomyItemInterface $taxonomyItem
    ): array {
        $schemaPropertyValue = $this->getSchemaObjectPropertyValueByPropertyPath(
            $schemaObject,
            $taxonomyItem->getSchemaObjectProperty()
        );
        $terms               = is_array($schemaPropertyValue) ? $schemaPropertyValue : [$schemaPropertyValue];
        $terms               = array_map(fn ($term) => $term instanceof BaseType && $term->hasProperty('name') ? $term->getProperty('name') : $term, $terms);
        return array_filter($terms);
    }

    /**
     * Get property value by property path.
     *
     * @param BaseType $schemaObject
     * @param string $propertyPath Name of a property or path to a nested property. Example: 'name', 'address.street'
     * @return array
     */
    private function getSchemaObjectPropertyValueByPropertyPath(mixed $schemaObject, string $propertyPath): array
    {
        $propertyPathParts = explode('.', $propertyPath);
        $propertyValue     = $schemaObject;

        if ($propertyValue instanceof BaseType) {
            $propertyValue = $propertyValue->getProperty($propertyPathParts[0]);
        }

        if (count($propertyPathParts) === 1) {
            if ($propertyValue instanceof BaseType) {
                $propertyValue = $propertyValue->getProperty($propertyPath);
            } elseif ($propertyValue instanceof PropertyValue) {
                $propertyValue = $propertyValue->getProperty('value');
            } elseif (is_array($propertyValue)) {
                return array_map(
                    fn($item) => $this->getSchemaObjectPropertyValueByPropertyPath($item, $propertyPath),
                    $propertyValue
                );
            }

            return !is_array($propertyValue) ? [$propertyValue] : $propertyValue;
        }

        $restPaths = implode('.', array_slice($propertyPathParts, 1));
        return $this->getSchemaObjectPropertyValueByPropertyPath($propertyValue, $restPaths);
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

            $insertedTerm = $this->wpService->insertTerm($term->name, $taxonomy);

            if (is_array($insertedTerm) && isset($insertedTerm['term_id'])) {
                return $insertedTerm['term_id'];
            }

            return null;
        }, $terms);

        return array_filter(array_map('intval', $termIds));
    }
}
