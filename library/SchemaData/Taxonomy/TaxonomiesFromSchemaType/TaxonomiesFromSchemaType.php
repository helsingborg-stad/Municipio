<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;

class TaxonomiesFromSchemaType implements TaxonomiesFromSchemaTypeInterface
{
    public function __construct(
        private TaxonomyFactoryInterface $taxonomyFactory,
        private SchemaToPostTypeResolverInterface $schemaToPostTypeResolver
    ) {
    }

    public function create(string $schemaType): array
    {
        return [
            'JobPosting' => $this->getJobPostingTaxonomies(),
            'Event'      => $this->getEventTaxonomies(),
            'Project'    => $this->getProjectTaxonomies(),
        ][$schemaType] ?? [];
    }

    private function getJobPostingTaxonomies(): array
    {
        return [
            $this->createTaxonomy('JobPosting', 'relevantOccupation', 'Job Categories', 'Job Category'),
            $this->createTaxonomy('JobPosting', 'validThrough', 'Latest Application Dates', 'Latest Application Date'),
        ];
    }

    private function getEventTaxonomies(): array
    {
        return [
            $this->createTaxonomy('Event', 'keywords.name', 'Event Tags', 'Event Tag'),
            $this->createTaxonomy('Event', 'physicalAccessibilityFeatures', 'Physical Accessibility Features', 'Physical Accessibility Feature'),
        ];
    }

    private function getProjectTaxonomies(): array
    {
        return [
            $this->createTaxonomy('Project', 'department', 'Project Departments', 'Project Department'),
            $this->createTaxonomy('Project', '@meta.category', 'Project Categories', 'Project Category'),
            $this->createTaxonomy('Project', '@meta.technology', 'Project Technologies', 'Project Technology'),
            $this->createTaxonomy('Project', 'status', 'Project Statuses', 'Project Status'),
        ];
    }

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
