<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GoogleMapField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\UrlField;
use WpService\Contracts\__;

class PlaceFormFactory implements FormFactoryInterface
{
    public function __construct(
        private __ $wpService,
    ) {
    }

    public function createForm(BaseType $schema): array
    {
        return [
            new StringField('telephone', $this->wpService->__('Telephone', 'municipio'), $schema->getProperty('telephone')),
            new UrlField('url', $this->wpService->__('URL', 'municipio'), $schema->getProperty('url')),
            new GoogleMapField('geo', $this->wpService->__('Geo Location', 'municipio'), $schema->getProperty('geo')),
        ];
    }
}
