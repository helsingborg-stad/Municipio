<?php

namespace Municipio\Helper;

class Template
{
    public function __construct()
    {
        add_action('template_redirect', array($this, 'loadTemplateClass'));
    }

    /**
     * Initializes helper classes for core template files
     * @return void
     */
    public function loadTemplateClass()
    {
        $template = get_page_template();
        $class = basename($template, '.blade.php');

        if (!file_exists(MUNICIPIO_PATH . 'library/Template/Core/' . $class . '.php')) {
            return false;
        }

        $class = '\Municipio\Template\Core\\' . $class;
        return new $class($template);
    }

    /**
     * Add a template
     * @param string $templateName Template name
     * @param string $templatePath Template path (relative to theme path)
     */
    public static function add($templateName, $templatePath)
    {
        add_filter('theme_page_templates', function ($templates) use ($templatePath, $templateName) {
            return array_merge(array(
                $templatePath => $templateName
            ), $templates);
        });

        return (object) array(
            'name' => $templateName,
            'path' => $templatePath
        );
    }
}
