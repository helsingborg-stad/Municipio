<?php

namespace Municipio\Customizer;

use Kirki\Compatibility\Kirki;
use Municipio\Customizer;
use Municipio\Customizer\PanelsRegistry;
use Municipio\Helper\KirkiConditional;

class KirkiField
{
    public static function addField(array $field): void
    {
        if (NativeField::supports($field)) {
            NativeField::addField($field);
            return;
        }

        PanelsRegistry::getInstance()->addRegisteredField($field);

        Kirki::add_field(Customizer::KIRKI_CONFIG, $field);
    }

    public static function addProField($instance)
    {
        Kirki::add_field($instance);
    }

    public static function addConditionalField(array $fields, array $toggle)
    {
        if (self::isAssocArray($fields)) {
            $fields = [$fields];
        }

        foreach ($fields as $field) {
            PanelsRegistry::getInstance()->addRegisteredField($field);
        }

        KirkiConditional::add_field(Customizer::KIRKI_CONFIG, $fields, $toggle);
    }

    private static function isAssocArray($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
