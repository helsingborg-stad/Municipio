<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use WpService\Contracts\__;

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
        private SchemaToPostTypeResolverInterface $schemaToPostTypeResolver,
        private __ $wpService
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
            $this->createTaxonomy('JobPosting', 'relevantOccupation', $this->wpService->__('Job Categories', 'municipio'), __('Job Category', 'municipio')),
            $this->createTaxonomy('JobPosting', 'validThrough', $this->wpService->__('Latest Application Dates', 'municipio'), __('Latest Application Date', 'municipio')),
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
            $this->createTaxonomy('Event', 'keywords.name', $this->wpService->__('Event Tags', 'municipio'), $this->wpService->__('Event Tag', 'municipio')),
            $this->createTaxonomy('Event', 'physicalAccessibilityFeatures', $this->wpService->__('Physical Accessibility Features', 'municipio'), $this->wpService->__('Physical Accessibility Feature', 'municipio')),
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
            $this->createTaxonomy('Project', 'department', $this->wpService->__('Project Departments', 'municipio'), $this->wpService->__('Project Department', 'municipio')),
            $this->createTaxonomy('Project', '@meta.category', $this->wpService->__('Project Categories', 'municipio'), $this->wpService->__('Project Category', 'municipio')),
            $this->createTaxonomy('Project', '@meta.technology', $this->wpService->__('Project Technologies', 'municipio'), $this->wpService->__('Project Technology', 'municipio')),
            $this->createTaxonomy('Project', 'status', $this->wpService->__('Project Statuses', 'municipio'), $this->wpService->__('Project Status', 'municipio')),
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
