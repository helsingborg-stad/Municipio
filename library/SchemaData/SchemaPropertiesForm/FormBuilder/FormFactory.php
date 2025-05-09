<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder;

use Municipio\Schema\BaseType;
use Municipio\Schema\Event;
use Municipio\Schema\ExhibitionEvent;
use Municipio\Schema\JobPosting;
use Municipio\Schema\Place;
use Municipio\Schema\Project;
use Municipio\Schema\Schema;
use Municipio\Schema\StatusEnumeration;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldValue\RegisterFieldValueInterface;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\{
    DateField,
    DateTimeField,
    EmailField,
    FieldInterface,
    FieldWithSubFieldsInterface,
    GoogleMapField,
    GroupField,
    HiddenField,
    RepeaterField,
    SelectField,
    StringField,
    TabField,
    UrlField,
    WysiwygField
};
use WpService\Contracts\__;

class FormFactory implements FormFactoryInterface
{
    public const FIELD_PREFIX = 'schema_';

    public function __construct(private RegisterFieldValueInterface $registerFieldValue, private __ $wpService)
    {
    }

    public function createForm(BaseType $schema): array
    {
        $fields = $this->getFields($schema);
        $group  = new GroupField('schemaData', '', $fields);

        $this->registerFieldValues([$group]);

        return [
            'instructions' => 'Schema properties for ' . $schema->getType(),
            'title'        => "Schema properties for {$schema->getType()}",
            'fields'       => [$group->toArray()],
            'location'     => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'all',
                    ]
                ],
            ],
        ];
    }

    private function createPlaceForm(BaseType $schema): array
    {
        return [
            new StringField('telephone', $this->wpService->__('Telephone', 'municipio'), $schema->getProperty('telephone')),
            new UrlField('url', $this->wpService->__('URL', 'municipio'), $schema->getProperty('url')),
            new GoogleMapField('geo', $this->wpService->__('Geo Location', 'municipio'), $schema->getProperty('geo')),
        ];
    }

    private function createJobPostingForm(BaseType $schema): array
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
                $schema->getProperty('applicationContact'),
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

    private function createEventForm(BaseType $schema): array
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
                    new HiddenField('@type', '@type', 'Organization'),
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
                    new HiddenField('@type', '@type', 'Offer'),
                    new StringField('name', $this->wpService->__('Name', 'municipio')),
                    new StringField('price', $this->wpService->__('Price', 'municipio')),
                    new StringField('priceCurrency', $this->wpService->__('Currency', 'municipio')),
                    new StringField('url', $this->wpService->__('Booking URL', 'municipio')),
                ]
            ),
        ];
    }

    private function createProjectForm(BaseType $schema): array
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

    public function prepareFieldName(string $name): string
    {
        return self::FIELD_PREFIX . $name;
    }

    /**
     * Get the fields for the schema.
     *
     * @param BaseType $schema The schema object.
     *
     * @return FieldInterface[]|FieldWithSubFieldsInterface[] $fields
     */
    private function getFields(BaseType $schema): array
    {
        return match ($schema::class) {
            Place::class => $this->createPlaceForm($schema),
            JobPosting::class => $this->createJobPostingForm($schema),
            Event::class => $this->createEventForm($schema),
            Project::class => $this->createProjectForm($schema),
            default   => []
        };
    }

    /**
     * Register field values for the schema.
     *
     * @param FieldInterface[]|FieldWithSubFieldsInterface[] $fields
     *
     * @return void
     */
    private function registerFieldValues(array $fields): void
    {
        foreach ($fields as $field) {
            if ($field instanceof RepeaterField) {
                continue; // Repeater fields are handled separately.
            } elseif ($field instanceof FieldWithSubFieldsInterface) {
                $this->registerFieldValues($field->getSubFields());
            } else {
                $this->registerFieldValue($field);
            }
        }
    }

    private function registerFieldValue(FieldInterface $field): void
    {
        $this->registerFieldValue->register($field);
    }
}
