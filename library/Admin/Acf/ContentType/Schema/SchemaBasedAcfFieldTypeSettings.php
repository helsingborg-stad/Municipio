<?php 

namespace Municipio\Admin\Acf\ContentType\Schema;

class SchemaBasedAcfFieldTypeSettings {
    public function __construct(private string $schemaType) {}

    public function getFieldType(): string 
    {
        $fieldTypeMap = [
            'GeoCoordinates' => 'google_map', 
            'PostalAddress'  => 'group',
            'ImageObject'    => 'image',
            'URL'            => 'url',
            'Email'          => 'email',
            'Date'           => 'date_time_picker',
        ];

        return $fieldTypeMap[$this->schemaType] ?? 'text';
    }

    public function getFieldTypeSettings(): array 
    {
        $fieldTypeSettingsMap = [
            'GeoCoordinates' => [
                'center_lat' => '59.32932', 
                'center_lng' => '18.06858', 
                'zoom' => 6, 
                'height' => 500
            ],
        ];

        return $fieldTypeSettingsMap[$this->schemaType] ?? [];
    }
}