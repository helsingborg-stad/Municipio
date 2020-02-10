<?php

namespace Municipio\Helper;

class Template
{
    /**
     * Add a template
     * \Municipio\Helper\Template::add($templateName, $templatePath);
     * @param string $templateName Template name
     * @param string $templatePath Template path (relative to theme path)
     * @param array  $postTypes Post types that can use the template (string "all" for all public psot types)
     */
    public static function add($templateName, $templatePath, $postTypes = array('page'))
    {
        $templateFile = basename($templatePath);

        add_action('init', function () use ($templateName, $templatePath, $postTypes, $templateFile) {
            if ((is_string($postTypes) && $postTypes === 'all') || (is_array($postTypes) && in_array('all', $postTypes))) {
                $postTypes = array_keys(\Municipio\Helper\PostType::getPublic([]));
            }

            foreach ($postTypes as $postType) {
                add_filter('theme_' . $postType . '_templates', function ($templates) use ($templateFile, $templatePath, $templateName) {
                    return array_merge(array(
                        $templateFile => $templateName
                    ), $templates);
                });
            }
        }, 999);

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
        $searchPaths = array_merge(self::getViewPaths(), $additionalPaths);

        if (isset($searchPaths) && is_array($searchPaths) && !empty($searchPaths)) {
            foreach ($searchPaths as $path) {
                $file = $path . DIRECTORY_SEPARATOR . str_replace('.blade.php', '', basename($template)) . '.blade.php';

                if (!file_exists($file)) {
                    continue;
                }

                return $file;
            }
        } else {
            error_log("Muncipio error: No template search paths defined in " . __DIR__ . __FILE__);
        }

        return false;
    }

    /**
     * Creates view paths dynamicly 
     * @param  array    $viewPaths   All view paths that are statically entered.
     * @return array    $viewPaths  Contains all view paths avabile. 
     */
    public static function getViewPaths($viewPaths = array()) {
        
        $versions = apply_filters('Municipio/blade/view_versions', array_reverse(array("v1", "v2", "v3"))); 

        foreach($versions as $versionKey => $version) {
            $viewPaths[] = get_stylesheet_directory()  . DIRECTORY_SEPARATOR  . "views" . DIRECTORY_SEPARATOR . $version;
            $viewPaths[] = get_template_directory()    . DIRECTORY_SEPARATOR  . "views" . DIRECTORY_SEPARATOR . $version;
        }

        return apply_filters('Municipio/blade/view_paths', array_unique($viewPaths)); 
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
