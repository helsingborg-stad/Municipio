<?php

namespace Municipio\Helper;

class Template
{
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

    /**
     * Check if and where template exists
     * @param  string $template        Template file name
     * @param  array  $additionalPaths Additional search paths
     * @return bool                    False if not found else path to template file
     */
    public static function locateTemplate($template, $additionalPaths = array())
    {
        $defaultPaths = array(
            get_stylesheet_directory() . '/views',
            get_stylesheet_directory(),
            get_template_directory() . '/views',
            get_template_directory()
        );

        $searchPaths = array_merge($defaultPaths, $additionalPaths);

        foreach ($searchPaths as $path) {
            $file = $path . '/' . str_replace('.blade.php', '', basename($template)) . '.blade.php';

            if (!file_exists($file)) {
                continue;
            }

            return $file;
        }

        return false;
    }
}
