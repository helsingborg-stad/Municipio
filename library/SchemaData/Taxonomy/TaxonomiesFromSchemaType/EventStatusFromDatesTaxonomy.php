<?php

namespace Municipio\SchemaData\Taxonomy\TaxonomiesFromSchemaType;

use WpService\Contracts\_x;

/**
 * Class ExhibitionEventEndDateTaxonomy
 */
class EventStatusFromDatesTaxonomy implements TaxonomyInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private _x $wpService,
        private TaxonomyInterface $innerTaxonomy
    ) {
    }

    /**
     * Override to format startDate and endDate to "closed" if in the past, "planned" if in the future and "ongoing" if present.
     */
    public function formatTermValue(mixed $value, array $schema): string|array|null
    {
        // Assume $value is a date string (e.g., '2025-08-01')
        $startDate = $schema['startDate'] ?? null;
        $endDate   = $schema['endDate'] ?? null;

        $startTimestamp = is_null($startDate) ? null : strtotime($startDate);
        $endTimestamp   = is_null($endDate) ? null : strtotime($endDate);

        if (empty($startTimestamp)) {
            return '';
        }

        if (!empty($endTimestamp) && $endTimestamp < time()) {
            return $this->wpService->_x('Closed', 'Event schema status', 'municipio');
        } elseif ($startTimestamp > time()) {
            return $this->wpService->_x('Planned', 'Event schema status', 'municipio');
        } else {
            return $this->wpService->_x('Ongoing', 'Event schema status', 'municipio');
        }
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->innerTaxonomy->getName();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaType(): string
    {
        return $this->innerTaxonomy->getSchemaType();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(): string
    {
        return $this->innerTaxonomy->getSchemaProperty();
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypes(): array
    {
        return $this->innerTaxonomy->getObjectTypes();
    }

    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        return $this->innerTaxonomy->getArguments();
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->innerTaxonomy->getLabel();
    }

    /**
     * @inheritDoc
     */
    public function getSingularLabel(): string
    {
        return $this->innerTaxonomy->getSingularLabel();
    }
}
