<?php

namespace Municipio\SchemaData\Taxonomy;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType\TaxonomiesFactoryInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\HasTerm;
use WpService\Contracts\TermExists;
use WpService\Contracts\WpInsertTerm;
use WpService\Contracts\WpSetPostTerms;

/**
 * Class AddTermsToPostFromSchema
 *
 * This class is responsible for adding terms to a post from the schema data.
 */
class AddTermsToPostFromSchema implements Hookable
{
    /**
     * Constructor for the AddTermsToPostFromSchema class.
     *
     * @param TaxonomiesFactoryInterface $taxonomiesFactory The factory to create taxonomies from schema types.
     * @param TermFactoryInterface $termFactory The factory to create terms based on the schema.
     * @param AddAction&HasTerm&TermExists&WpInsertTerm&WpSetPostTerms $wpService The WordPress service for handling terms and actions.
     */
    public function __construct(
        private TaxonomiesFactoryInterface $taxonomiesFactory,
        private TermFactoryInterface $termFactory,
        private AddAction&HasTerm&TermExists&WpInsertTerm&WpSetPostTerms $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('updated_postmeta', [$this, 'addTermsToPostFromSchema'], 10, 4);
        $this->wpService->addAction('added_post_meta', [$this, 'addTermsToPostFromSchema'], 10, 4);
    }

    /**
     * Adds terms to a post based on the schema data stored in post meta.
     *
     * @param int $metaId The ID of the meta entry.
     * @param int $objectId The ID of the post object.
     * @param string $metaKey The key of the meta entry.
     * @param mixed $serializedSchema The serialized schema data.
     */
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

    /**
     * Unserializes the schema data.
     *
     * @param mixed $serializedSchema The serialized schema data.
     * @return array|null The unserialized schema data as an array, or null if unserialization fails.
     */
    private function unserializeSchema($serializedSchema)
    {
        if (is_array($serializedSchema)) {
            return $serializedSchema;
        }

        return is_string($serializedSchema) ? @unserialize($serializedSchema) : null;
    }

    /**
     * Retrieves taxonomies that match the given schema type.
     *
     * @param string $schemaType The schema type to match.
     * @return array An array of TaxonomyInterface objects that match the schema type.
     */
    private function getMatchingTaxonomies($schemaType): array
    {
        $taxonomies = $this->taxonomiesFactory->create();
        return array_filter(
            $taxonomies,
            fn($taxonomy) => $taxonomy->getSchemaType() === $schemaType
        );
    }

    /**
     * Creates terms from the provided taxonomies and schema data.
     *
     * @param array $taxonomies An array of TaxonomyInterface objects.
     * @param array $schema The schema data to use for creating terms.
     * @return array An array of WP_Term objects created from the taxonomies and schema.
     */
    private function createTermsFromTaxonomies(array $taxonomies, array $schema): array
    {
        $terms = array_map(fn($taxonomy) => $this->termFactory->create($taxonomy, $schema), $taxonomies);

        $foo = 'bar';

        return array_merge(...array_map(static fn($item) => is_array($item) ? $item : [$item], $terms));
    }

    /**
     * Groups terms by their taxonomy.
     *
     * @param array $terms An array of WP_Term objects.
     * @return array An associative array where keys are taxonomy names and values are arrays of terms.
     */
    private function groupTermsByTaxonomy(array $terms): array
    {
        $grouped = [];
        foreach ($terms as $term) {
            $grouped[$term->taxonomy][] = $term;
        }
        return $grouped;
    }

    /**
     * Ensures that the terms exist in the specified taxonomy, inserting them if they do not.
     *
     * @param array $terms An array of WP_Term objects to ensure exist.
     * @param string $taxonomy The taxonomy in which to ensure the terms exist.
     */
    private function ensureTermsExist(array $terms, string $taxonomy): void
    {
        foreach ($terms as $term) {
            if (!$this->wpService->termExists($term->name, $taxonomy)) {
                $this->wpService->wpInsertTerm($term->name, $taxonomy);
            }
        }
    }

    /**
     * Assigns the specified terms to the post in the given taxonomy.
     *
     * @param int $objectId The ID of the post object.
     * @param array $terms An array of WP_Term objects to assign to the post.
     * @param string $taxonomy The taxonomy in which to assign the terms.
     */
    private function assignTermsToPost($objectId, array $terms, string $taxonomy): void
    {
        $termNames = array_map(static fn($term) => $term->name, $terms);
        $this->wpService->wpSetPostTerms($objectId, implode(',', $termNames), $taxonomy, false);
    }
}
