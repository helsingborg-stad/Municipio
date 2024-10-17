<?php

namespace Municipio\SchemaData\Utils;

use WpService\Contracts\ApplyFilters;

/**
 * Class GetEnabledSchemaTypes
 *
 * @package Municipio\SchemaData\Utils
 */
class GetEnabledSchemaTypes implements GetEnabledSchemaTypesInterface
{
    /**
     * Constructor
     */
    public function __construct(private ApplyFilters $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function getEnabledSchemaTypesAndProperties(): array
    {
        $typesAndProps = array(
            'Place'               => array('geo', 'telephone', 'url'),
            'School'              => array(),
            'Project'             => array(
                '@id',
                'description',
                'name',
                'department',
                'employee',
                'funding',
            ),
            'JobPosting'          => array(
                '@id',
                'applicationContact',
                'datePosted',
                'description',
                'directApply',
                'employerOverview',
                'employmentType',
                'employmentUnit',
                'hiringOrganization',
                'relevantOccupation',
                'url',
                'validThrough',
            ),
            'SpecialAnnouncement' => array(
                '@id',
                'description',
                'datePosted',
                'name',
            ),
        );

        return $this->wpService->applyFilters('Municipio/SchemaData/EnabledSchemaTypes', $typesAndProps);
    }
}
