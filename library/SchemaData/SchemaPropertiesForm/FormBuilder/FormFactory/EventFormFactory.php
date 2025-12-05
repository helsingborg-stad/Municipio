<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\DateTimeField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\EmailField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GoogleMapField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\HiddenField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\RepeaterField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\SelectField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\TabField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\TypeField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\UrlField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\WysiwygField;
use WpService\Contracts\__;

/**
 * Class EventFormFactory
 *
 * This class is responsible for creating a form for the event schema.
 */
class EventFormFactory implements FormFactoryInterface
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
            new StringField('name', $this->wpService->__('Name', 'municipio'), $schema->getProperty('name')),
            new WysiwygField('description', $this->wpService->__('Description', 'municipio'), $schema->getProperty('description')),
            new SelectField('eventStatus', $this->wpService->__('Event Status', 'municipio'), $schema->getProperty('eventStatus'), [
                \Municipio\Schema\EventStatusType::EventScheduled   => $this->wpService->__('Event Scheduled', 'municipio'),
                \Municipio\Schema\EventStatusType::EventPostponed   => $this->wpService->__('Event Postponed', 'municipio'),
                \Municipio\Schema\EventStatusType::EventCancelled   => $this->wpService->__('Event Cancelled', 'municipio'),
                \Municipio\Schema\EventStatusType::EventMovedOnline => $this->wpService->__('Event Moved Online', 'municipio'),
                \Municipio\Schema\EventStatusType::EventRescheduled => $this->wpService->__('Event Rescheduled', 'municipio'),
            ]),
            new SelectField('eventAttendanceMode', $this->wpService->__('Event Attendance Mode', 'municipio'), $schema->getProperty('eventAttendanceMode'), [
                \Municipio\Schema\EventAttendanceModeEnumeration::OfflineEventAttendanceMode => $this->wpService->__('Offline Event Attendance Mode', 'municipio'),
                \Municipio\Schema\EventAttendanceModeEnumeration::OnlineEventAttendanceMode  => $this->wpService->__('Online Event Attendance Mode', 'municipio'),
                \Municipio\Schema\EventAttendanceModeEnumeration::MixedEventAttendanceMode   => $this->wpService->__('Mixed Event Attendance Mode', 'municipio'),
            ]),
            new StringField('url', $this->wpService->__('URL for more information', 'municipio'), $schema->getProperty('url')),
            new StringField('typicalAgeRange', $this->wpService->__('Typical Age Range', 'municipio'), $schema->getProperty('typicalAgeRange')),
            new TabField('', $this->wpService->__('Time & Location', 'municipio')),
            new DateTimeField('startDate', $this->wpService->__('Start Date', 'municipio'), $schema->getProperty('startDate')),
            new DateTimeField('endDate', $this->wpService->__('End Date', 'municipio'), $schema->getProperty('endDate')),
            new GoogleMapField('location', $this->wpService->__('Location', 'municipio'), $schema->getProperty('location')),
            new TabField('', $this->wpService->__('Organizer', 'municipio')),
            new RepeaterField(
                'organizer',
                $this->wpService->__('Organizer', 'municipio'),
                is_array($schema->getProperty('organizer')) ? $schema->getProperty('organizer') : [],
                [
                    new TypeField('Organization'),
                    new StringField('name', $this->wpService->__('Name', 'municipio')),
                    new EmailField('email', $this->wpService->__('Email', 'municipio')),
                    new StringField('telephone', $this->wpService->__('Telephone', 'municipio')),
                    new UrlField('url', $this->wpService->__('URL', 'municipio')),
                ]
            ),
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
