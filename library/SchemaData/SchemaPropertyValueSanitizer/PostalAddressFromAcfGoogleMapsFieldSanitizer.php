<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

use Municipio\Admin\Acf\ContentType\Schema\Subfields\PostalAddress;

class PostalAddressFromAcfGoogleMapsFieldSanitizer implements SchemaPropertyValueSanitizer
{
    private mixed $value;
    private array $allowedTypes;

    public function __construct(private $inner = new NullSanitizer())
    {
    }

    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        $this->value        = $value;
        $this->allowedTypes = $allowedTypes;

        if ($this->shouldSanitize()) {
            return $this->getPostalAddressFromValue();
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }

    private function shouldSanitize(): bool
    {
        return
            in_array('PostalAddress', $this->allowedTypes) &&
            is_array($this->value) &&
            isset($this->value['lat']) &&
            isset($this->value['lng']);
    }

    private function getPostalAddressFromValue(): \Spatie\SchemaOrg\PostalAddress
    {
        $postalAddress  = new \Spatie\SchemaOrg\PostalAddress();
        $streetAddress  = $this->value['street_number'] ?? '';
        $streetAddress .= ' ' . $this->value['street_name'] ?? '';

        $postalAddress['name']             = $this->value['name'] ?? null;
        $postalAddress['streetAddress']    = $streetAddress ?: null;
        $postalAddress['addressLocality']  = $this->value['city'] ?? null;
        $postalAddress['addressRegion']    = $this->value['state'] ?? null;
        $postalAddress['postalCode']       = $this->value['post_code'] ?? null;
        $postalAddress['addressCountry']   = $this->value['country'] ?? null;
        $postalAddress['geo']              = new \Spatie\SchemaOrg\GeoCoordinates();
        $postalAddress['geo']['latitude']  = $this->value['lat'] ?? null;
        $postalAddress['geo']['longitude'] = $this->value['lng'] ?? null;

        return $postalAddress;
    }
}
