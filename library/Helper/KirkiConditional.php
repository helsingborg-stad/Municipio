<?php

namespace Municipio\Helper;

class KirkiConditional
{
  /**
   * Adds a kirki field, with a on/off button for toggling the field.
   *
   * This helper takes associative arrays (single field configurations) and
   * none associative arrays as an argument for field configurations.
   * None Associative arrays are interpreted as multiple field configurations.
   *
   * When using a multi field configuration, we strongly recomment to provide
   * a settings and label parameter in the toggle configuration parameter.
   * This enables you to configure a clear and sparated name for the activation field.
   *
   * @param string  $kirkiConfig   The kirki config id.
   * @param array   $fieldConfig   The field conguration(s).
   * @param array   $toggleConfig  The toggle configuration,
   *                               should only be used if multiple
   *                               fieldconfigs exist
   * @return void
   */
    // phpcs:ignore 
    public static function add_field($kirkiConfig, $fieldConfigs, $toggleConfig = [])
    {
        //Convert to nested array
        if (self::isAssocArray($fieldConfigs)) {
            $fieldConfigs = [$fieldConfigs];
        }

        //Use toggle config if it exists
        $toggleConfig = array_merge([
          'label'    => esc_html__('Tailor', 'municipio') . " " . strtolower($fieldConfigs[0]['label']),
          'settings' => $fieldConfigs[0]['settings'] . '_active'
        ], array_filter($toggleConfig));

        //Activation field
        \Kirki::add_field($kirkiConfig, [
          'type'     => 'toggle',
          'settings' => $toggleConfig['settings'],
          'label'    => $toggleConfig['label'],
          'default'  => false,
          'priority' => 10,
          'section'  => $fieldConfigs[0]['section'],
          'choices'  => [
            true  => esc_html__('Enable', 'municipio'),
            false => esc_html__('Disable', 'municipio'),
          ]
        ]);

        foreach ($fieldConfigs as $fieldConfig) {
            \Kirki::add_field($kirkiConfig, array_merge(
                $fieldConfig,
                ['active_callback' => [
                  [
                    'setting'  => $toggleConfig['settings'],
                    'operator' => '===',
                    'value'    => true,
                  ]
                ]]
            ));
        }
    }

    /**
     * Function to check if array is associative
     *
     * @param array $array
     * @return boolean
     */
    private static function isAssocArray($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
