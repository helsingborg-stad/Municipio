<?php

namespace Municipio\Customizer\Source;

class CustomizerSidebarHelper
{
    private $sidebars = array(), $enabledSidebars = array();

    private $args = array(
        'description'   => '',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    );

    public function enabledSidebars()
    {
        return $this->enabledSidebars;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function mergeArgs($args)
    {
        if (!is_array($args) || empty($args) ) {
            return false;
        }

        $this->args = array_merge($this->args, $args);
        return true;
    }

    public function register($id, $name, $description = '')
    {
        return $this->registerSidebar($id, $name, $description);
    }

    public function registerSidebar($id, $name, $description = '')
    {
        if (!is_string($name) || empty($name) || !is_string($id) || empty($id)) {
            return false;
        }

        $args = array_merge(['name' => $name, 'id' => $id], $this->args);

        if (!empty($description) && is_string($description)) {
            $args['description'] = __($description, 'municipio');
        }

        register_sidebar($args);

        $this->enabledSidebars[$args['id']] = $args;

        return true;
    }
}
