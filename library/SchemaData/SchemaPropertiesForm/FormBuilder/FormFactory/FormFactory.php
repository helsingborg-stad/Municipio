<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\Schema\Event;
use Municipio\Schema\JobPosting;
use Municipio\Schema\Place;
use Municipio\Schema\Project;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldValue\RegisterFieldValueInterface;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\{
    FieldInterface,
    FieldWithSubFieldsInterface,
    GroupField,
    RepeaterField,
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
            Place::class => (new PlaceFormFactory($this->wpService))->createForm($schema),
            JobPosting::class => (new JobPostingFormFactory($this->wpService))->createForm($schema),
            Event::class => (new EventFormFactory($this->wpService))->createForm($schema),
            Project::class => (new ProjectFormFactory($this->wpService))->createForm($schema),
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
