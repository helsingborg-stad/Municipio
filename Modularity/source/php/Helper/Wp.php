<?php

namespace Modularity\Helper;

class Wp
{
    /**
     * Get core templates
     * @return array Core templates found
     */
    public static function getCoreTemplates($extension = false)
    {
        $paths = apply_filters('Modularity/CoreTemplatesSearchPaths', array(
            get_stylesheet_directory(),
            get_template_directory()
        ));

        $fileExt = apply_filters('Modularity/CoreTemplatesSearchFileExtension', array(
            '.php',
            '.blade.php'
        ));

        $search = apply_filters(
            'Modularity/CoreTemplatesSearchTemplates',
            array(
                'index',
                'comments',
                'front-page',
                'home',
                'single',
                'single-*',
                'archive',
                'archive-*',
                'page',
                'page-*',
                'category',
                'category-*',
                'author',
                'date',
                'search',
                'attachment',
                'image'
            )
        );

        $templates = array();

        foreach ($paths as $path) {
            foreach ($search as $pattern) {
                foreach ($fileExt as $ext) {
                    $foundTemplates = array();
                    foreach (glob($path . '/' . $pattern . $ext) as $found) {
                        $basename = str_replace(array('.blade.php', '.php'), '', basename($found));

                        if ($extension) {
                            $foundTemplates[$basename] = basename($found);
                        } else {
                            $foundTemplates[$basename] = str_replace(array('.blade.php', '.php'), '', basename($found));
                        }
                    }

                    $templates = array_merge($templates, $foundTemplates);
                }
            }
        }

        $templates = array_unique($templates);

        return $templates;
    }

    /**
     * Get core templates
     * @return array Core templates found
     */
    public static function findCoreTemplates($templates = null, $extension = false)
    {
        $paths = apply_filters('Modularity/CoreTemplatesSearchPaths', array(
            get_stylesheet_directory(),
            get_template_directory()
        ));

        $fileExt = apply_filters('Modularity/CoreTemplatesSearchFileExtension', array(
            '.php',
            '.blade.php'
        ));

        $search = $templates;

        if (is_null($search) || !is_array($search)) {
            return false;
        }

        foreach ($paths as $path) {
            foreach ($search as $pattern) {
                foreach ($fileExt as $ext) {
                    $foundTemplates = array();
                    foreach (glob($path . '/' . $pattern . $ext) as $found) {
                        $basename = str_replace(array('.blade.php', '.php'), '', basename($found));

                        if ($extension) {
                            return basename($found);
                        } else {
                            return str_replace(array('.blade.php', '.php'), '', basename($found));
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Tries to get the template path
     * Checks the plugin's template folder, the parent theme's templates folder and the current theme's template folder
     * @param  string  $prefix The filename without prefix
     * @param  string  $slug   The directory
     * @param  boolean $error  Show errors or not
     * @return string          The path to the template to use
     */
    public static function getTemplate($prefix = '', $slug = '', $error = true)
    {
        $paths = apply_filters('Modularity/Module/TemplatePath', array(
            get_stylesheet_directory() . '/templates/',
            get_template_directory() . '/templates/',
            MODULARITY_PATH . 'templates/',
        ));

        $slug = apply_filters('Modularity/TemplatePathSlug', $slug ? $slug . '/' : '', $prefix);
        $prefix = $prefix ? '-' . $prefix : '';

        foreach ($paths as $path) {
            $file = $path . $slug . 'modularity' . $prefix . '.php';

            if (file_exists($file)) {
                return $file;
            }
        }

        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            error_log('Modularity: Template ' . $slug . 'modularity' . $prefix . '.php' . ' not found in any of the paths: ' . var_export($paths, true));
            if ($error) {
                trigger_error('Modularity: Template ' . $slug . 'modularity' . $prefix . '.php' . ' not found in any of the paths: ' . var_export($paths, true), E_USER_WARNING);
            }
        }
    }

    /**
     * Gets site information
     * @return array
     */
    public static function getSiteInfo()
    {
        $siteInfo = array(
            'name' => get_bloginfo('name'),
            'url' => esc_url(home_url('/')),
        );

        return $siteInfo;
    }

    public static function getThemeMod(string $key = '')
    {
        if ($key == '') {
            return get_theme_mods();
        }
        return get_theme_mod($key);
    }
    /**
     * Check if the add question form is opened in thickbox iframe
     * @return boolean
     */
    public static function isThickBox()
    {
        // Check if thickbox is set in query string
        if (isset($_GET['is_thickbox']) && $_GET['is_thickbox'] == 'true') {
            return true;
        }

        // Check if referer is thickbox
        $referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
        if (is_string($referer) && strpos($referer, 'is_thickbox=true') > -1) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if editor mode (Modularity)
     * @return boolean
     */
    public static function isEditor()
    {
        return isset($_GET['page']) && $_GET['page'] == 'modularity-editor';
    }

    /**
     * Check if gutenberg editor mode (Modularity)
     * @return boolean
     */
    public static function isGutenbergEditor()
    {
        global $post;

        if (!$post && isset($_GET['page_for']) && !empty($_GET['page_for'])) {
            $post = get_post($_GET['page_for']);
        }

        if (use_block_editor_for_post($post)) {
            return $post->ID;
        }
        return false;
    }

    /**
     * Fetches current archive slug
     * @return mixed (string, boolean) False if not archive else archive slug
     */
    public static function getArchiveSlug()
    {
        global $wp_query;

        if ($wp_query && !is_tax() && (is_post_type_archive() || is_archive() || is_home() || is_search() || is_404())) {
            $postType = get_post_type();

            if (isset($wp_query->query_vars['post_type']) && !empty($wp_query->query_vars['post_type'])) {
                $postType = $wp_query->query_vars['post_type'];
            }

            if (is_home()) {
                $archiveSlug = 'archive-post';
            } elseif (is_post_type_archive() && is_search()) {
                $archiveSlug = 'archive-' . get_post_type_object($postType)->name;
            } elseif (is_search()) {
                $archiveSlug = 'search';
            } elseif (is_404()) {
                $archiveSlug = 'e404';
            } elseif (is_author()) {
                $archiveSlug = 'author';
            } else {
                $archiveSlug = 'archive-' . get_post_type_object($postType)->name;
            }

            return $archiveSlug;
        }

        return false;
    }

    /**
     * Get the slug of the current single post or page.
     *
     * @return string|null The slug of the current single post or page, or null if not found.
     */
    public static function getSingleSlug(): ?string
    {
        $postType = get_post_type();
        return $postType ? 'single-' . $postType : null;
    }

    public static function deprecatedFunction($message)
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            trigger_error($message);
        } else {
            echo $message;
        }
    }

    /**
     * Check if current page is add new/edit post
     * @return boolean
     */
    public static function isAddOrEditOfPostType($postType)
    {
        global $current_screen;

        return $current_screen->base == 'post'
                && $current_screen->id == $postType
                && (
                    $current_screen->action == 'add' || (isset($_GET['action']) && $_GET['action'] == 'edit')
                );
    }
}
