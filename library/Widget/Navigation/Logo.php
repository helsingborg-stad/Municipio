<?php

namespace Municipio\Widget\Navigation;

class Logo extends \Municipio\Widget\Source\BaseWidget
{
    public function setup()
    {
        $widget = array(
            'id'            => 'navigation_logo',
            'name'          => 'Navigation Logotype',
            'description'   => 'Display website logotype, used in navigation',
            'template'      => 'navigation-logo.blade.php'
        );

        return $widget;
    }

    public function init($args, $instance)
    {
        if ($this->get_field('widget_navigation_logotype')) {
            switch ($this->checkFiletype($this->get_field('widget_navigation_logotype'))) {
                case 'svg':
                    $path = \Municipio\Helper\Image::urlToPath($this->get_field('widget_navigation_logotype')['url']);
                    $this->data['logotype'] = \Municipio\Helper\Svg::extract($path);
                break;
                case 'png':
                    $this->data['logotype'] = '<img src="' . $this->get_field('widget_navigation_logotype')['url'] . '">';
                break;
            }
        } else {
            $this->data['logotype'] = '<h1>' . get_bloginfo('name') . '</h1>';
        }

        $this->data['home'] = get_bloginfo('url');
    }

    public function checkFiletype($attachment)
    {
        if (!isset($attachment['url']) || !is_string($attachment['url'])) {
            return false;
        }

        $url = pathinfo($attachment['url']);

        if (isset($url['extension'])) {
            return $url['extension'];
        }

        return false;
    }
}
