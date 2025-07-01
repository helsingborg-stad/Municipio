<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFactoryInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\HasTerm;
use WpService\Contracts\TermExists;
use WpService\Contracts\WpInsertTerm;
use WpService\Contracts\WpSetPostTerms;

class AddTermsToPostFromSchema implements Hookable
{
    public function __construct(
        private TaxonomiesFactoryInterface $taxonomiesFactory,
        private TermFactoryInterface $termFactory,
        private AddAction&HasTerm&TermExists&WpInsertTerm&WpSetPostTerms $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('updated_postmeta', [$this, 'addTermsToPostFromSchema'], 10, 4);
    }

    public function addTermsToPostFromSchema($metaId, $objectId, $metaKey, $serializedSchema): void
    {
        if ($metaKey !== 'schemaData') {
            return;
        }

        $schema = $this->unserializeSchema($serializedSchema);
        if (!$schema || !isset($schema['@type'])) {
            return;
        }

        $taxonomies = $this->getMatchingTaxonomies($schema['@type']);
        $terms      = $this->createTermsFromTaxonomies($taxonomies, $schema);

        $termsByTaxonomy = $this->groupTermsByTaxonomy($terms);

        foreach ($termsByTaxonomy as $taxonomy => $termsInTaxonomy) {
            $this->ensureTermsExist($termsInTaxonomy, $taxonomy);
            $this->assignTermsToPost($objectId, $termsInTaxonomy, $taxonomy);
        }
    }

    private function unserializeSchema($serializedSchema)
    {
        return @unserialize($serializedSchema) ?: null;
    }

    private function getMatchingTaxonomies($schemaType): array
    {
        $taxonomies = $this->taxonomiesFactory->create();
        return array_filter(
            $taxonomies,
            fn($taxonomy) => $taxonomy->getSchemaType() === $schemaType
        );
    }

    private function createTermsFromTaxonomies(array $taxonomies, array $schema): array
    {
        $terms = array_map(fn($taxonomy) => $this->termFactory->create($taxonomy, $schema), $taxonomies);

        $foo = 'bar';

        return array_merge(...array_map(static fn($item) => is_array($item) ? $item : [$item], $terms));
    }

    private function groupTermsByTaxonomy(array $terms): array
    {
        $grouped = [];
        foreach ($terms as $term) {
            $grouped[$term->taxonomy][] = $term;
        }
        return $grouped;
    }

    private function ensureTermsExist(array $terms, string $taxonomy): void
    {
        foreach ($terms as $term) {
            if (!$this->wpService->termExists($term->name, $taxonomy)) {
                $this->wpService->wpInsertTerm($term->name, $taxonomy);
            }
        }
    }

    private function assignTermsToPost($objectId, array $terms, string $taxonomy): void
    {
        $termNames = array_map(static fn($term) => $term->name, $terms);
        $this->wpService->wpSetPostTerms($objectId, implode(',', $termNames), $taxonomy, false);
    }
}
