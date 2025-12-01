<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\MultilineStringField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use WpService\Contracts\__;

/**
 * Class SpecialAnnouncementFormFactory
 *
 * This class is responsible for creating a form for the special announcement schema.
 */
class SpecialAnnouncementFormFactory implements FormFactoryInterface
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
            new StringField('name', $this->wpService->__('Name', 'municipio'), $schema->getProperty('name')),
            new MultilineStringField('description', $this->wpService->__('Description', 'municipio'), $schema->getProperty('description')),
        ];
    }
}
