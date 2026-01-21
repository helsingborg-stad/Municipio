<?php

namespace Municipio\SchemaData\Taxonomy;

use WpService\Contracts\GetTerms;
use WpService\Contracts\WpDeleteTerm;

/**
 * Class CleanupUnusedTerms
 * This class is responsible for cleaning up unused terms from taxonomies defined in the schema.
 */
class CleanupUnusedTerms
{
    /**
     * CleanupUnusedTerms constructor.
     *
     * @param TaxonomiesFromSchemaType\TaxonomiesFactoryInterface $taxonomiesFactory
     * @param GetTerms&WpDeleteTerm $wpService
     */
    public function __construct(
        private TaxonomiesFromSchemaType\TaxonomiesFactoryInterface $taxonomiesFactory,
        private GetTerms&WpDeleteTerm $wpService,
    ) {}

    /**
     * Cleans up unused terms from the taxonomies defined in the schema.
     * This method retrieves all terms for the defined taxonomies, filters out those that are not used,
     * and deletes them.
     */
    public function cleanupUnusedTerms(): void
    {
        $taxonomyNames = $this->getTaxonomyNames();

        if (count($taxonomyNames) === 0) {
            return;
        }

        $terms = $this->getAllTerms($taxonomyNames);
        $unusedTerms = $this->filterUnusedTerms($terms);

        $this->deleteTerms($unusedTerms);
    }

    /**
     * Retrieves the names of all taxonomies defined in the schema.
     *
     * @return array An array of taxonomy names.
     */
    private function getTaxonomyNames(): array
    {
        $taxonomies = $this->taxonomiesFactory->create();
        return array_map(fn($taxonomy) => $taxonomy->getName(), $taxonomies);
    }

    /**
     * Retrieves all terms for the specified taxonomies.
     *
     * @param array $taxonomyNames An array of taxonomy names.
     * @return array An array of WP_Term objects.
     */
    private function getAllTerms(array $taxonomyNames): array
    {
        return $this->wpService->getTerms([
            'taxonomy' => $taxonomyNames,
            'hide_empty' => false,
        ]);
    }

    /**
     * Filters the terms to find those that are not used (count === 0).
     *
     * @param array $terms An array of WP_Term objects.
     * @return array An array of unused WP_Term objects.
     */
    private function filterUnusedTerms(array $terms): array
    {
        return array_filter($terms, fn($term) => $term->count === 0);
    }

    /**
     * Deletes the specified terms from the database.
     *
     * @param array $terms An array of WP_Term objects to delete.
     */
    private function deleteTerms(array $terms): void
    {
        foreach ($terms as $term) {
            $this->wpService->wpDeleteTerm($term->term_id, $term->taxonomy);
        }
    }
}
