<?php

namespace Municipio\SchemaData\Utils;

/**
 * Class GetEnabledSchemaTypes
 *
 * @package Municipio\SchemaData\Utils
 */
class GetEnabledSchemaTypes implements GetEnabledSchemaTypesInterface
{
    /**
     * @inheritDoc
     */
    public function getEnabledSchemaTypesAndProperties(): array
    {
        return array(
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
    }
}
