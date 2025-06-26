<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\Schema\DayOfWeek;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\DateField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\DateTimeField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GalleryField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GoogleMapField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GroupField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\MultiSelectField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\RepeaterField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\RequiredField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\TabField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\TimeField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\TypeField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\WysiwygField;
use WpService\Contracts\__;

/**
 * Class ExhibitionEventFormFactory
 *
 * This class is responsible for creating a form for the exhibition event schema.
 */
class ExhibitionEventFormFactory implements FormFactoryInterface
{
    /**
     * Constructor.
     *
     * @param __ $wpService The WordPress service instance.
     */
    public function __construct(
        private __ $wpService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function createForm(BaseType $schema): array
    {
        return [
            new TabField('', $this->wpService->__('General Information', 'municipio')),
            new RequiredField(new StringField('name', $this->wpService->__('Name', 'municipio'), $schema->getProperty('name'))),
            new WysiwygField('description', $this->wpService->__('About the exhibition', 'municipio'), $schema->getProperty('description')),
            new GalleryField('image', $this->wpService->__('Gallery', 'municipio'), $schema->getProperty('image')),
            new TabField('', $this->wpService->__('Time & Location', 'municipio')),
            new DateTimeField('startDate', $this->wpService->__('Start Date', 'municipio'), $schema->getProperty('startDate')),
            new DateTimeField('endDate', $this->wpService->__('End Date', 'municipio'), $schema->getProperty('endDate')),
            new GroupField(
                'location',
                $this->wpService->__('Location', 'municipio'),
                [
                    new TypeField('Place'),
                    new StringField('name', $this->wpService->__('Name', 'municipio'), $schema->getProperty('location')?->getProperty('name') ?? null),
                    new GoogleMapField('geo', $this->wpService->__('Geo', 'municipio'), $schema->getProperty('location')?->getProperty('geo')),
                ]
            ),
            // TODO: Add description for location.
            new TabField('', $this->wpService->__('Opening Hours', 'municipio')),
            new RepeaterField(
                'openingHoursSpecification',
                $this->wpService->__('Opening Hours', 'municipio'),
                is_array($schema->getProperty('openingHoursSpecification')) ? $schema->getProperty('openingHoursSpecification') : [],
                [
                    new TypeField('OpeningHoursSpecification'),
                    new MultiSelectField('dayOfWeek', $this->wpService->__('Day of the week', 'municipio'), null, [
                        DayOfWeek::Monday    => $this->wpService->__('Monday', 'municipio'),
                        DayOfWeek::Tuesday   => $this->wpService->__('Tuesday', 'municipio'),
                        DayOfWeek::Wednesday => $this->wpService->__('Wednesday', 'municipio'),
                        DayOfWeek::Thursday  => $this->wpService->__('Thursday', 'municipio'),
                        DayOfWeek::Friday    => $this->wpService->__('Friday', 'municipio'),
                        DayOfWeek::Saturday  => $this->wpService->__('Saturday', 'municipio'),
                        DayOfWeek::Sunday    => $this->wpService->__('Sunday', 'municipio'),
                    ]),
                    new TimeField('opens', $this->wpService->__('Opens', 'municipio')),
                    new TimeField('closes', $this->wpService->__('Closes', 'municipio')),
                    new DateField('validFrom', $this->wpService->__('Valid From', 'municipio')),
                    new DateField('validThrough', $this->wpService->__('Valid Through', 'municipio')),
                ]
            ),
            new RepeaterField('specialOpeningHoursSpecification', $this->wpService->__('Special Opening Hours', 'municipio'), is_array($schema->getProperty('specialOpeningHoursSpecification')) ? $schema->getProperty('specialOpeningHoursSpecification') : [], [
                new TypeField('SpecialOpeningHoursSpecification'),
                new StringField('name', $this->wpService->__('Name', 'municipio')),
                new DateTimeField('opens', $this->wpService->__('Opens', 'municipio')),
                new DateTimeField('closes', $this->wpService->__('Closes', 'municipio')),
            ]),
            new TabField('', $this->wpService->__('Offers', 'municipio')),
            new RepeaterField(
                'offers',
                $this->wpService->__('Offers', 'municipio'),
                $schema->getProperty('offers') ?? [],
                [
                    new TypeField('Offer'),
                    new StringField('name', $this->wpService->__('Name', 'municipio')),
                    new StringField('price', $this->wpService->__('Price', 'municipio')),
                    new StringField('priceCurrency', $this->wpService->__('Currency', 'municipio')),
                    new StringField('url', $this->wpService->__('Booking URL', 'municipio')),
                ]
            ),
        ];
    }
}
