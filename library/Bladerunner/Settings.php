<?php

namespace Municipio\Bladerunner;

class Settings
{
    public function __construct()
    {
        $this->createCacheFolder();

        add_filter('bladerunner/template_types', array($this, 'templateTypes'));
        add_filter('bladerunner/get_post_template', array($this, 'getPostTemplate'));
        add_filter('bladerunner/cache_path', array($this, 'cachePath'));
        add_filter('bladerunner/cache', array($this, 'cachePath'));
    }

    /**
     * Gets the post's/page's template
     * @param  string $template The default template
     * @return string           The template to use
     */
    public function getPostTemplate($template)
    {
        global $post;
        $templateAfter = $template;

        // Custom page templates
        if (get_page_template_slug($post->ID)) {
            $templateAfter = get_page_template_slug($post->ID);
        }

        // Taxonomy templates
        $object = get_queried_object();
        if (is_a($object, 'WP_Term')) {
            $templateAfter = locate_template('taxonomy-' . $object->taxonomy . '.blade.php');
            if (empty($templateAfter)) {
                $templateAfter = $template;
            }
        }

        return apply_filters('municipio/template_slug', $templateAfter);
    }

    /**
     * Filter of template types
     */
    public function templateTypes($types)
    {
        return array_merge(array(
            'index'      => 'index.blade.php',
            'home'       => 'index.blade.php',
            'single'     => 'single.blade.php',
            'page'       => 'page.blade.php',
            '404'        => '404.blade.php',
            'archive'    => 'archive.blade.php',
            'author'     => 'author.blade.php',
            'category'   => 'category.blade.php',
            'tag'        => 'tag.blade.php',
            'taxonomy'   => 'taxonomy.blade.php',
            'date'       => 'date.blade.php',
            'front-page' => 'front-page.blade.php',
            'paged'      => 'paged.blade.php',
            'search'     => 'search.blade.php',
            'single'     => 'single.blade.php',
            'singular'   => 'singular.blade.php',
            'attachment' => 'attachment.blade.php'
        ), $types);
    }

    /**
     * Get the cache path
     * @return string
     */
    public function cachePath()
    {
        return WP_CONTENT_DIR . '/uploads/cache/blade-cache';
    }

    /**
     * Creates the template cache folder if missing
     * @return void
     */
    public function createCacheFolder()
    {
        if (file_exists($this->cachePath())) {
            return false;
        }

        mkdir($this->cachePath(), 0777, true);
        chmod($this->cachePath(), 0777);

        return true;
    }
}
