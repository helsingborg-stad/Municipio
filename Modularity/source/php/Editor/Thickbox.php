<?php

declare(strict_types=1);

namespace Modularity\Editor;

use WpUtilService\Features\Enqueue\EnqueueManager;

class Thickbox
{
    public function __construct(
        protected EnqueueManager $wpEnqueue,
    ) {
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
        add_action('admin_head', [$this, 'addJsVariables']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
        add_action('current_screen', function ($current_screen) {
            add_action('views_edit-' . $current_screen->post_type, [$this, 'addFilterUrlParams']);
        });
    }

    /**
     * Add thickbox parameter to filter links (All, Publish, Trash etc.)
     * @param array $views Default links
     */
    public function addFilterUrlParams($views)
    {
        foreach ($views as $index => $view) {
            $views[$index] = preg_replace(
                "/(?<=href=(\"|'))[^\"']+(?=(\"|'))/",
                '${0}&is_thickbox=true',
                $views[$index],
            );
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

        if (
            substr($current_screen->post_type, 0, 4) == 'mod-'
            && ($current_screen->action == 'add' || $current_screen->action == '')
        ) {
            echo
                '
                <script>
                    var modularity_post_id = '
                . $id
                . ";
                    var modularity_post_action = '"
                . $current_screen->action
                    . "';
                </script>
            "
            ;
        }
    }

    /**
     * Enqueue scripts and styles specific for the Thickbox content
     * @return void
     */
    public function enqueue()
    {
        // Script
        $this->wpEnqueue?->add('js/modularity-editor-modal.js', [], '1.0.0', true)->add(
            'css/modularity-thickbox-edit.css',
        );
    }
}
