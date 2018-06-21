<?php

namespace Municipio\Widget\Media;

class Media extends \Municipio\Widget\Source\WidgetTemplate
{
    public function setup()
    {
        $widget = array(
            'id'            => 'media-municipio',
            'name'          => 'Media widget',
            'description'   => 'Display media',
            'template'      => 'media.media-widget',
            'fields'        => array('utilityFields')
        );

        return $widget;
    }

    public function viewController($args, $instance)
    {
        //Image or text logotype
        if ($this->get_field('image')) {
            switch ($this->checkFiletype($this->get_field('image'))) {
                case 'svg':
                    $path = \Municipio\Helper\Image::urlToPath($this->get_field('image')['url']);
                    $this->data['image'] = \Municipio\Helper\Svg::extract($path);
                    $this->data['imageRatio'] = $this->getSvgRatio($path);
                break;
                case 'png':
                    $this->data['image'] = '<img src="' . $this->get_field('image')['url'] . '">';
                break;
            }
        }

        if ($this->get_field('media_link') == 'internal' && !empty($this->get_field('internal_link'))) {
            $this->data['url'] = $this->get_field('internal_link');
        } elseif ($this->get_field('media_link') == 'external' && !empty($this->get_field('external_link'))) {
            $this->data['url'] = $this->get_field('external_link');
        }
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

    public function getSvgRatio($file)
    {
        $dimensions = array();

        if ($file && $xml = simplexml_load_file($file)) {
            $viewBox = list($x_start, $y_start, $x_end, $y_end) = explode(' ', $xml['viewBox']);

            if (count($viewBox) == 4) {
                $dimensions['width']    = (int) ($viewBox[2] - $viewBox[0]);
                $dimensions['height']   = (int) ($viewBox[3] - $viewBox[1]);
                return abs(round(($dimensions['height'] / $dimensions['width']) * 100, 2));
            }
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
