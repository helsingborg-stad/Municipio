<?php

namespace Municipio\Widget\Header;

class Logo extends \Municipio\Widget\Source\HeaderWidget
{
    public function setup()
    {
        $widget = array(
            'id'            => 'widget-header-logo',
            'name'          => 'Header widget: Logo',
            'description'   => 'Display website logotype, used in header',
            'template'      => 'header-logo.blade.php'
        );

        return $widget;
    }

    public function viewController($args, $instance)
    {
        $this->data['maxWidth'] = 999;

        //Image or text logotype
        if ($this->get_field('widget_header_logotype')) {
            switch ($this->checkFiletype($this->get_field('widget_header_logotype'))) {
                case 'svg':
                    $path = \Municipio\Helper\Image::urlToPath($this->get_field('widget_header_logotype')['url']);
                    $this->data['logotype'] = \Municipio\Helper\Svg::extract($path);
                break;
                case 'png':
                    $this->data['logotype'] = '<img src="' . $this->get_field('widget_header_logotype')['url'] . '">';
                break;
            }

            if ($maxWidth = $this->get_field('widget_header_max_width')) {
                $this->data['maxWidth'] = $maxWidth;
            }

        } else {
            $this->data['logotype'] = '<h1>' . get_bloginfo('name') . '</h1>';
        }

        $this->data['home'] = get_bloginfo('url');

        $this->data['language'] = array(
            'logoLabel' => __("Go to homepage", 'municipio'),
        );
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


    /**
     * Available methods & vars for BaseWidget and extensions:
     *
     * @method array setup() Used to construct the widget instance. Required return array keys: id, name, description & template
     * @method void viewController($args, $instance) Used to send data to the view;
     *
     *
     */
}
