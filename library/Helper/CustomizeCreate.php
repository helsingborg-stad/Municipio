<?php

namespace Municipio\Helper;

class CustomizeCreate
{
  
  /**
   * Default setup of section
   */
    public function __construct($panel, $sections)
    {
        try {
            //Main panel name
            $panelId = $this->registerPanel($panel);

            //Register provided sections
            if (!empty($sections) && is_array($sections)) {
                foreach ($sections as $section) {
                    if (isset($section['active']) && $section['active'] === true) {
                        $this->registerSection($panelId, $section);
                    }
                }
            }
        } catch (\Exception $e) {
            wp_die($e, __("Municipio customizer error", 'municipio'));
        }
    }

    /**
     * Register panel
     *
     * @return void
     */
    public function registerPanel($panel)
    {
        if (function_exists('acf_add_customizer_panel')) {
            return acf_add_customizer_panel($panel);
        }
        return new \Exception('Cound not run du to missing acf_add_customizer_panel');
    }

    /**
     * Registers a new panel.
     *
     * @param string $name
     * @return void
     */
    public function registerSection($panelId, $section)
    {
        if (is_array($section)) {
            acf_add_customizer_section(array_merge(['panel' => $panelId], $section));
        }
    }
}
