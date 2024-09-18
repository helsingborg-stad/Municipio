<?php

namespace Municipio\Customizer\Controls;

class Typography
{
    public function __construct()
    {
        add_filter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_dynamic_css', array($this, 'fixCssVarOutputForFontVariantFields'), 10, 1);
    }

    public function fixCssVarOutputForFontVariantFields($styles)
    {
        foreach (self::getCssVarOutputFromFontVariantFields() as $selector => $outputs) {
            $cssFromOutput = implode(' ', array_map(function ($output) {
                return "{$output['property']} : {$output['value']};";
            }, $outputs));

            $styles .= "
                {$selector}{
                    {$cssFromOutput}
                }
            ";
        }

        return $styles;
    }

    private static function getCssVarOutputFromFontVariantFields()
    {
        $selectors = [];
        foreach (\Kirki\Compatibility\Kirki::$all_fields as $fieldKey => $field) {
            if (!empty($field['output'])) {
                foreach ($field['output'] as $output) {
                    if (
                        !empty($output['choice']) && $output['choice'] === 'variant'
                        && !empty($output['property']) && self::isValidCssVar($output['property'])
                        && !empty(get_theme_mod($fieldKey, [])['variant'])
                    ) {
                        $selectors[$output['element']]   = $selectors[$output['element']] ?? [];
                        $selectors[$output['element']][] = [
                            'property' => $output['property'],
                            'value'    => self::replaceStringWeights(get_theme_mod($fieldKey, [])['variant']),
                        ];
                    }
                }
            }
        }

        return $selectors;
    }

    private static function isValidCssVar($string)
    {
        return !empty(preg_match('/(--)[^\,\:\)]+/', $string));
    }

    private static function replaceStringWeights($value)
    {
        $weights = apply_filters('Municipio/Customizer/Controls/Typography:variant_weights', [
            'regular' => 400,
        ]);

        return in_array($value, array_keys($weights)) ? $weights[$value] : $value;
    }
}
