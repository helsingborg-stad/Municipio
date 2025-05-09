<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\DateField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\EmailField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GroupField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\HiddenField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\RepeaterField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\UrlField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\WysiwygField;
use WpService\Contracts\__;

class JobPostingFormFactory implements FormFactoryInterface
{
    public function __construct(
        private __ $wpService,
    ) {
    }

    public function createForm(BaseType $schema): array
    {
        return [
            new UrlField('url', $this->wpService->__('Application URL', 'municipio'), $schema->getProperty('url')),
            new DateField('datePosted', $this->wpService->__('Date Posted', 'municipio'), $schema->getProperty('datePosted')),
            new DateField('validThrough', $this->wpService->__('Valid Through', 'municipio'), $schema->getProperty('validThrough')),
            new StringField('@id', $this->wpService->__('Reference', 'municipio'), $schema->getProperty('@id')),
            new WysiwygField('description', $this->wpService->__('Description', 'municipio'), $schema->getProperty('description')),
            new StringField('employerOverview', $this->wpService->__('Employer Overview', 'municipio'), $schema->getProperty('employerOverview')),
            new StringField('employmentType', $this->wpService->__('Employment Type', 'municipio'), $schema->getProperty('employmentType')),
            new GroupField(
                'employmentUnit',
                $this->wpService->__('Employment Unit', 'municipio'),
                [
                    new HiddenField('@type', '@type', 'Organization'),
                    new StringField('name', $this->wpService->__('Name', 'municipio'), $schema->getProperty('employmentUnit')?->getProperty('name') ?? null),
                    new GroupField(
                        'address',
                        $this->wpService->__('Address', 'municipio'),
                        [
                            new HiddenField('@type', '@type', 'PostalAddress'),
                            new StringField('addressRegion', $this->wpService->__('Address Region', 'municipio'), $schema->getProperty('employmentUnit')?->getProperty('address')?->getProperty('addressRegion') ?? null),
                            new StringField('addressLocality', $this->wpService->__('Address Locality', 'municipio'), $schema->getProperty('employmentUnit')?->getProperty('address')?->getProperty('addressLocality') ?? null)
                        ]
                    ),
                ]
            ),
            new GroupField('hiringOrganization', $this->wpService->__('Hiring Organization', 'municipio'), [
                new HiddenField('@type', '@type', 'Organization'),
                new UrlField('ethicsPolicy', $this->wpService->__('Ethics Policy', 'municipio'), $schema->getProperty('hiringOrganization')?->getProperty('ethicsPolicy') ?? null),
            ]),
            new RepeaterField(
                'applicationContact',
                $this->wpService->__('Application Contact', 'municipio'),
                $schema->getProperty('applicationContact') ?? [],
                [
                    new HiddenField('@type', '@type', 'ContactPoint'),
                    new StringField('name', $this->wpService->__('Name', 'municipio')),
                    new EmailField('email', $this->wpService->__('Email', 'municipio')),
                    new StringField('telephone', $this->wpService->__('Telephone', 'municipio')),
                    new StringField('contactType', $this->wpService->__('Contact Type', 'municipio')),
                ]
            ),
        ];
    }
}
