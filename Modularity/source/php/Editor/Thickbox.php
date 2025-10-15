<?php

namespace Modularity\Editor;

class Thickbox
{
    public function __construct()
    {
        if (\Modularity\Helper\Wp::isThickBox()) {
            $this->init();
        }
    }

    /**
     * Initializes the class if we're in a thickbox (checked in the __construct method)
     * @return void
     */
    public function init()
    {
        add_action('admin_head', array($this, 'addJsVariables'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
        add_action('current_screen', function ($current_screen) {
            add_action('views_edit-' . $current_screen->post_type, array($this, 'addFilterUrlParams'));
        });
    }

    /**
     * Add thickbox parameter to filter links (All, Publish, Trash etc.)
     * @param array $views Default links
     */
    public function addFilterUrlParams($views)
    {
        foreach ($views as $index => $view) {
            $views[$index] = preg_replace("/(?<=href=(\"|'))[^\"']+(?=(\"|'))/", '${0}&is_thickbox=true',  $views[$index]);
        }

        return $views;
    }

    /**
     * Adds required javascript variables to the thickbox page
     * @return void
     */
    public function addJsVariables()
    {
        global $current_screen;
        global $post;
        global $archive;

        $id = isset($post->ID) ? $post->ID : "'" . $archive . "'";

        if (substr($current_screen->post_type, 0, 4) == 'mod-' && ($current_screen->action == 'add' || $current_screen->action == '')) {
            echo "
                <script>
                    var modularity_post_id = ". $id . ";
                    var modularity_post_action = '" . $current_screen->action . "';
                </script>
            ";
        }
    }

    /**
     * Enqueue scripts and styles specific for the Thickbox content
     * @return void
     */
    public function enqueue()
    {
        // Script
        wp_register_script(
            'modularity-thickbox', 
            MODULARITY_URL . '/dist/' . \Modularity\Helper\CacheBust::name('js/modularity-editor-modal.js'),
            [],
            '1.0.0',
            true
        );
        wp_enqueue_script('modularity-thickbox');

        // Style
        wp_register_style(
            'modularity-thickbox', 
            MODULARITY_URL . '/dist/' . \Modularity\Helper\CacheBust::name('css/modularity-thickbox-edit.css')
        );
        wp_enqueue_style('modularity-thickbox');
    }
}
