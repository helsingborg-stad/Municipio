<?php

namespace Municipio\Helper;

class KirkiCondidional
{
    public static function add_field($kirkiConfig, $fieldConfig)
    {

        //Activation field
        \Kirki::add_field($kirkiConfig, [
          'type'        => 'switch',
          'settings'    => $fieldConfig['settings'] . '_active',
          'label'       => esc_html__('Enable', 'municipio') . " " . strtolower($fieldConfig['label']),
          'default'     => false,
          'priority'    => 10,
          'section'     => $fieldConfig['section'],
          'choices'     => [
            true  => esc_html__('Enable', 'municipio'),
            false => esc_html__('Disable', 'municipio'),
          ]
        ]);

        \Kirki::add_field($kirkiConfig, array_merge(
            $fieldConfig,
            ['active_callback'  => [
              [
                'setting'  => $fieldConfig['settings'] . '_active',
                'operator' => '===',
                'value'    => true,
              ]
            ]]
        ));
    }
}
