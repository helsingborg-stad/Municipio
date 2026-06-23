<?php

namespace Municipio\Customizer;

class KirkiField
{
    public static function addField(array $field): void
    {
        if (NativeField::supports($field)) {
            NativeField::addField($field);
            return;
        }

        if (CustomField::supports($field)) {
            CustomField::addField($field);
            return;
        }

        PanelsRegistry::getInstance()->addRegisteredField($field);
    }

    public static function addProField($instance)
    {
        if (is_array($instance)) {
            self::addField($instance);
        }
    }

    public static function addConditionalField(array $fields, array $toggle)
    {
        if (self::isAssocArray($fields)) {
            $fields = [$fields];
        }

        $toggle = array_merge([
            'type' => 'toggle',
            'settings' => $fields[0]['settings'] . '_active',
            'label' => esc_html__('Tailor', 'municipio') . ' ' . strtolower($fields[0]['label']),
            'default' => false,
            'priority' => 10,
            'section' => $fields[0]['section'],
            'choices' => [
                true => esc_html__('Enable', 'municipio'),
                false => esc_html__('Disable', 'municipio'),
            ],
        ], array_filter($toggle));

        self::addField($toggle);

        foreach ($fields as $field) {
            self::addField(array_merge($field, [
                'active_callback' => [
                    [
                        'setting' => $toggle['settings'],
                        'operator' => '===',
                        'value' => true,
                    ],
                ],
            ]));
        }
    }

    private static function isAssocArray($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
