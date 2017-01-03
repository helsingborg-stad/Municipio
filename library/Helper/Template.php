<?php

namespace Municipio\Helper;

class Template
{
    /**
     * Add a template
     * \Municipio\Helper\Template::add($templateName, $templatePath);
     * @param string $templateName Template name
     * @param string $templatePath Template path (relative to theme path)
     */
    public static function add($templateName, $templatePath)
    {
        $templateFile = basename($templatePath);

        add_filter('theme_page_templates', function ($templates) use ($templateFile, $templatePath, $templateName) {
            return array_merge(array(
                $templateFile => $templateName
            ), $templates);
        });

        return (object) array(
            'name' => $templateName,
            'path' => $templateFile,
            'fullPath' => $templatePath
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

    /**
     * Check if template has blade.php extension
     * @param  string  $template Template path
     * @return boolean
     */
    public static function isBlade($template)
    {
        if (!preg_match('/(blade.php)$/i', $template)) {
            return false;
        }

        return true;
    }
}
