<?php

namespace Municipio\Customizer\Source;

abstract class CustomizerController
{
    public function addThemeModClasses($keys, $themeMods, $elementAttribute, $ignoreThisValues = array())
    {
        if (empty($themeMods) || empty($keys)) {
            return $elementAttribute;
        }

        $keys = (is_string($keys)) ? array($keys) : $keys;
        $ignoreThisValues = (is_string($ignoreThisValues)) ? [$ignoreThisValues] : $ignoreThisValues;

        foreach ($keys as $key) {
            if (!isset($themeMods[$key])) {
                continue;
            }

            if (!empty($ignoreThisValues) && in_array($themeMods[$key], $ignoreThisValues)) {
                continue;
            }

            $elementAttribute->addClass($themeMods[$key]);
        }

        return $elementAttribute;
    }

    public function addThemeModGridClasses($format, $themeMods, $elementAttribute)
    {
        $gridKeys = $this->gridKeys($format);

        if (!isset($themeMods[$gridKeys[0]])) {
            $elementAttribute->addClass('grid-xs-12');
        }

        $elementAttribute = $this->addThemeModClasses($gridKeys, $themeMods, $elementAttribute);

        return $elementAttribute;
    }

    public function getModsByPrefix($prefix)
    {
        $mods = array();

        foreach (array_keys(get_theme_mods()) as $key) {
            if (is_int(strrpos($key, $prefix)) && strrpos($key, $prefix) == 0) {
                $mods[str_replace($prefix, '', $key)] = get_theme_mod($key);
            }
        }

        return $mods;
    }

    public function gridKeys($format = 'column-size-%s')
    {
        if (empty(\Municipio\Helper\Css::Breakpoints())) {
            return;
        }

        $gridKeys = array();
        foreach (\Municipio\Helper\Css::Breakpoints() as $breakpoint) {
            $gridKeys[] = sprintf($format, $breakpoint);
        }

        return $gridKeys;
    }
}
