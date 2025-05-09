<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\DateField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GroupField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\HiddenField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\SelectField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\WysiwygField;
use WpService\Contracts\__;

class ProjectFormFactory implements FormFactoryInterface
{
    public function __construct(
        private __ $wpService,
    ) {
    }

    public function createForm(BaseType $schema): array
    {
        return [
            new StringField('name', $this->wpService->__('Name', 'municipio'), $schema->getProperty('name')),
            new WysiwygField('description', $this->wpService->__('Description', 'municipio'), $schema->getProperty('description')),
            new DateField('foundingDate', $this->wpService->__('Founding Date', 'municipio'), $schema->getProperty('foundingDate')),
            new GroupField(
                'department',
                $this->wpService->__('Department', 'municipio'),
                [
                    new HiddenField('@type', '@type', 'Organization'),
                    new StringField('name', $this->wpService->__('Name', 'municipio'), $schema->getProperty('department')?->getProperty('name') ?? null),
                ]
            ),
            new GroupField(
                'funding',
                $this->wpService->__('Funding', 'municipio'),
                [
                    new HiddenField('@type', '@type', 'MonetaryGrant'),
                    new StringField('amount', $this->wpService->__('Amount', 'municipio'), $schema->getProperty('funding')?->getProperty('amount') ?? null)
                ]
            ),
            new GroupField(
                'status',
                $this->wpService->__('Status', 'municipio'),
                [
                    new HiddenField('@type', '@type', 'ProgressStatus'),
                    new StringField('name', $this->wpService->__('Name', 'municipio'), $schema->getProperty('status')?->getProperty('name') ?? null),
                    new HiddenField('maxNumber', 'maxNumber', 100),
                    new HiddenField('minNumber', 'minNumber', 0),
                    new SelectField('number', $this->wpService->__('Number', 'municipio'), $schema->getProperty('status')?->getProperty('number') ?? null, [
                        0   => '0%',
                        25  => '25%',
                        50  => '50%',
                        75  => '75%',
                        100 => '100%',
                    ]),
                ]
            ),
        ];
    }
}
