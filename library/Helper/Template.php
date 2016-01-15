<?php

namespace Municipio\Helper;

class Template
{
    public function __construct()
    {
        add_filter('bladerunner/get_post_template', array($this, 'loadTemplateClass'), 999);
    }

    /**
     * Initializes core template helper classes from /library/Template/Core/
     * @return void
     */
    public function loadTemplateClass($template)
    {
        $class = basename($template, '.php');
        $class = basename($class, '.blade');
        $class = ucwords($class, '-');
        $class = str_replace('-', '', $class);

        if (!file_exists(MUNICIPIO_PATH . 'library/Template/Core/' . $class . '.php')) {
            return $template;
        }

        $class = '\Municipio\Template\Core\\' . $class;
        new $class($template);

        return $template;
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
