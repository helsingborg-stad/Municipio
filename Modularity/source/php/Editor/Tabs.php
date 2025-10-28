<?php

namespace Modularity\Editor;

class Tabs
{
    protected $tabs = array();

    public function __construct()
    {
        add_action('edit_form_top', array($this, 'output'));
    }

    /**
     * Outputs the tabbar html
     * @param  integer $activeIndex Current active tab index
     * @return void
     */
    public function output()
    {
        if (!$this->shouldOutput()) {
            return false;
        }

        echo '<h2 class="modularity-nav-tab-wrapper" id="modularity-tabs">';

        $requestUri = str_replace('&message=1', '', $_SERVER['REQUEST_URI']);

        foreach ($this->tabs as $tab => $url) {
            if (strpos($url, $requestUri) !== false || (strpos($url, 'post.php') !== false && strpos($requestUri, 'post-new.php') !== false)) {
                echo '<a href="' . $url . '" class="nav-tab nav-tab-active">' . $tab . '</a>';
            } else {
                echo '<a href="' . $url . '" class="nav-tab">' . $tab . '</a>';
            }
        }

        echo '<div class="modularity-clearfix"></div></h2>';
    }

    /**
     * Add a tab to the tabbar
     * @param string $title The tab title text
     * @param string $url   The target url
     */
    public function add($title, $url)
    {
        $this->tabs[$title] = $url;
        return true;
    }

    /**
     * Check if tabs should be outputted
     * @return boolean
     */
    protected function shouldOutput()
    {
        global $current_screen;

        $options = get_option('modularity-options');
        $enabled = isset($options['enabled-post-types']) && is_array($options['enabled-post-types']) ? $options['enabled-post-types'] : array();

        $validPostType = in_array($current_screen->id, $enabled);

        $action = $current_screen->action;
        if (empty($action)) {
            $action = (isset($_GET['action']) && !empty($_GET['action'])) ? $_GET['action'] : null;
        }

        $validAction = in_array($action, array(
            'add',
            'edit'
        ));

        return ($validPostType && $validAction) || $current_screen->id == 'admin_page_modularity-editor';
    }
}
