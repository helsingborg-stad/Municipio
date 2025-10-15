<?php

namespace Modularity\Helper;

class Template
{
    /**
     * Search for a specific template (view)
     * @param  string             $view   View name (filename)
     * @param  \Modularity\Module $module
     * @return string                     Found template/view
     */
    public static function getModuleTemplate($view, $module, $sanitizeTemplateName = false)
    {
        $view = basename($view, '.blade.php');
        $view = basename($view, '.php');

        // Paths to search
        $paths = array_unique(array(
            $module->templateDir
        ));

        //General filter
        $paths = apply_filters(
            'Modularity/Module/TemplatePath', 
            $paths
        );

        //Specific filter
        $paths = apply_filters(
            'Modularity/Module/' . $module->slug . '/TemplatePath', 
            $paths
        );

        // Search for blade template
        foreach ($paths as $path) {
            $filename = $view . '.blade.php';

            $fileWithSubfolder = trailingslashit($path) . trailingslashit($module->post_type) . $filename;
            if (\Modularity\Helper\File::fileExists($fileWithSubfolder)) {
                if($sanitizeTemplateName) {
                    return self::santitizeTemplateName($fileWithSubfolder);
                }
                return $fileWithSubfolder;
            }

            $fileWithoutSubfolder = trailingslashit($path) . $filename;
            if (\Modularity\Helper\File::fileExists($fileWithoutSubfolder)) {
                if($sanitizeTemplateName) {
                    return self::santitizeTemplateName($fileWithoutSubfolder);
                }
                return $fileWithoutSubfolder;
            }
        }

        return false;
    }

    /**
     * Sanitize a template name by removing file extensions.
     *
     * This function takes a template name as input and removes common file extensions (".blade.php" and ".php").
     * It returns the sanitized template name without any file extensions.
     *
     * @param string $template The template name to sanitize.
     * @return string The sanitized template name without file extensions.
     */
    private static function santitizeTemplateName($template) {
        $view       = basename($template, '.blade.php');
        $view       = basename($view, '.php');
        return $view;
    }
}
