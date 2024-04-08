<?php 

namespace Municipio\Admin\Acf\ContentType\Schema\Subfields;

class SchemaBasedAcfSubfieldsHandler {
    public function __construct(private string $schemaData) {}

    public function getSubfields(): array 
    {
        $class = 'Municipio\\Admin\\Acf\\ContentType\\Schema\Subfields\\' . $this->schemaData;

        if (class_exists($class)) {
            $schemaData = new $class();
            
            return $schemaData->getSubFields();
        }

        return [];
    }
}
