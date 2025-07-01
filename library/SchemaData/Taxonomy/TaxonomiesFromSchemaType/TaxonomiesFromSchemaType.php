<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;

/**
 * Class TaxonomiesFromSchemaType
 *
 * This class is responsible for creating taxonomies based on the schema type.
 * It uses a factory to create taxonomies and a resolver to map schema types to post types.
 */
class TaxonomiesFromSchemaType implements TaxonomiesFromSchemaTypeInterface
{
    /**
     * TaxonomiesFromSchemaType constructor.
     *
     * @param TaxonomyFactoryInterface $taxonomyFactory The factory to create taxonomies.
     * @param SchemaToPostTypeResolverInterface $schemaToPostTypeResolver The resolver to map schema types to post types.
     */
    public function __construct(
        private TaxonomyFactoryInterface $taxonomyFactory,
        private SchemaToPostTypeResolverInterface $schemaToPostTypeResolver
    ) {
    }

    /**
     * Create taxonomies based on the schema type.
     *
     * @param string $schemaType The schema type for which to create taxonomies.
     * @return array An array of TaxonomyInterface objects.
     */
    public function create(string $schemaType): array
    {
        return [
            'JobPosting' => $this->getJobPostingTaxonomies(),
            'Event'      => $this->getEventTaxonomies(),
            'Project'    => $this->getProjectTaxonomies(),
        ][$schemaType] ?? [];
    }

    /**
     * Get taxonomies for JobPosting schema type.
     *
     * @return array An array of TaxonomyInterface objects for JobPosting.
     */
    private function getJobPostingTaxonomies(): array
    {
        return [
            $this->createTaxonomy('JobPosting', 'relevantOccupation', 'Job Categories', 'Job Category'),
            $this->createTaxonomy('JobPosting', 'validThrough', 'Latest Application Dates', 'Latest Application Date'),
        ];
    }

    /**
     * Get taxonomies for Event schema type.
     *
     * @return array An array of TaxonomyInterface objects for Event.
     */
    private function getEventTaxonomies(): array
    {
        return [
            $this->createTaxonomy('Event', 'keywords.name', 'Event Tags', 'Event Tag'),
            $this->createTaxonomy('Event', 'physicalAccessibilityFeatures', 'Physical Accessibility Features', 'Physical Accessibility Feature'),
        ];
    }

    /**
     * Get taxonomies for Project schema type.
     *
     * @return array An array of TaxonomyInterface objects for Project.
     */
    private function getProjectTaxonomies(): array
    {
        return [
            $this->createTaxonomy('Project', 'department', 'Project Departments', 'Project Department'),
            $this->createTaxonomy('Project', '@meta.category', 'Project Categories', 'Project Category'),
            $this->createTaxonomy('Project', '@meta.technology', 'Project Technologies', 'Project Technology'),
            $this->createTaxonomy('Project', 'status', 'Project Statuses', 'Project Status'),
        ];
    }

    /**
     * Create a taxonomy based on the schema type and property.
     *
     * @param string $schemaType The schema type for the taxonomy.
     * @param string $schemaProperty The schema property for the taxonomy.
     * @param string $label The label for the taxonomy.
     * @param string $singularLabel The singular label for the taxonomy.
     * @return TaxonomyInterface The created taxonomy.
     */
    private function createTaxonomy(
        string $schemaType,
        string $schemaProperty,
        string $label,
        string $singularLabel,
    ): TaxonomyInterface {
        return $this->taxonomyFactory->create(
            $schemaType,
            $schemaProperty,
            $this->schemaToPostTypeResolver->resolve($schemaType),
            $label,
            $singularLabel
        );
    }
}
