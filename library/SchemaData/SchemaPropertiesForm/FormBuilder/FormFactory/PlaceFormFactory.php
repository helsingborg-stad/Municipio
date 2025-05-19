<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GoogleMapField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\UrlField;
use WpService\Contracts\__;

/**
 * Class PlaceFormFactory
 *
 * This class is responsible for creating a form for the place schema.
 */
class PlaceFormFactory implements FormFactoryInterface
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
            new StringField('telephone', $this->wpService->__('Telephone', 'municipio'), $schema->getProperty('telephone')),
            new UrlField('url', $this->wpService->__('URL', 'municipio'), $schema->getProperty('url')),
            new GoogleMapField('geo', $this->wpService->__('Geo Location', 'municipio'), $schema->getProperty('geo')),
        ];
    }
}
