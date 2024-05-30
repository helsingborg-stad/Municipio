<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Taxonomy\ITaxonomyItem;
use Municipio\ExternalContent\Taxonomy\ITaxonomyRegistrar;
use Municipio\ExternalContent\WpTermFactory\WpTermFactoryInterface;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\InsertTerm;
use WpService\Contracts\TermExists;

/**
 * Class WpPostMetaFactoryVersionDecorator
 */
class TermsDecorator implements WpPostFactoryInterface
{
    /**
     * WpPostMetaFactoryVersionDecorator constructor.
     *
     * @param ITaxonomyItem[] $taxonomyItems
     * @param WpPostMetaFactoryInterface $inner
     */
    public function __construct(
        private ITaxonomyRegistrar $taxonomyRegistrar,
        private WpTermFactoryInterface $wpTermFactory,
        private InsertTerm&TermExists $wpService,
        private WpPostFactoryInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(BaseType $schemaObject, ISource $source): array
    {
        $post                  = $this->inner->create($schemaObject, $source);
        $matchingTaxonomyItems = $this->tryGetMatchingTaxonomyItems($schemaObject);

        if (!isset($post['tax_input'])) {
            $post['tax_input'] = [];
        }

        foreach ($matchingTaxonomyItems as $taxonomyItem) {
            $propertyValue                               = $schemaObject->getProperty($taxonomyItem->getSchemaObjectProperty());
            $terms                                       = is_array($propertyValue) ? $propertyValue : [$propertyValue];
            $wpTerms                                     = array_map(function ($term) use ($taxonomyItem) {
                return $this->wpTermFactory->create($term, $taxonomyItem->getName());
            }, $terms);
            $termIds                                     = $this->getTermIdsFromTerms($wpTerms, $taxonomyItem->getName());
            $post['tax_input'][$taxonomyItem->getName()] = $termIds;
        }

        return $post;
    }

    private function tryGetMatchingTaxonomyItems(BaseType $schemaObject): array
    {
        return array_filter(
            $this->taxonomyRegistrar->getRegisteredTaxonomyItems(),
            fn(ITaxonomyItem $taxonomyItem) =>
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
