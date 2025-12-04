<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertiesFromMappedFields;

use Municipio\Helper\WpService;

/**
 * Class SchemaPropertiesFromMappedFieldsFactory
 */
class SchemaPropertiesFromMappedFieldsFactory implements SchemaPropertiesFromMappedFieldsFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(): SchemaPropertiesFromMappedFieldsInterface
    {
        return new SchemaPropertiesFromMappedFields(
            new \Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes(),
            [
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\GalleryHandler(WpService::get()),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\RepeaterWithSchemaObjectsHandler(new self()),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\GroupWithSchemaObjectHandler(new self()),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\GeoCoordinatesHandler(),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\EmailHandler(),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\DateTimeHandler(),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\TimeHandler(),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\DateHandler(),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\UrlHandler(),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\IntHandler(),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\TextHandler(),
                new \Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\MultiSelectHandler(),
            ]
        );
    }
}
