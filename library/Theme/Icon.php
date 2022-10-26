<?php

namespace Municipio\Theme;

/**
 * Class Icon
 * @package Municipio\Theme
 */
class Icon
{
    public function __construct()
    {
      add_filter(
          'ComponentLibrary/Component/Icon/AltTextPrefix',
          array($this, 'altTextPrefix'),
          10,
          1
      );

      add_filter(
          'ComponentLibrary/Component/Icon/AltText',
          array($this, 'AltText'),
          10,
          1
      );

      add_filter(
          'ComponentLibrary/Component/Icon/altTextUndefined',
          array($this, 'altTextUndefined'),
          10,
          1
      );
    }

    public function altTextPrefix($altTextPrefix)
    {
        return __("Icon: ", 'municipio');
    }

    public function altText($altText)
    {
        return [
          'language' => __("Globe", 'municipio'),
          'menu' => __("Menu", 'municipio'),
          'date_range' => __("Calendar", 'municipio'),
          'search' => __("Magnifying glass", 'municipio'),
          'print' => __("Printer", 'municipio'),
          'thumb_up' => __("Thumb up", 'municipio'),
          'thumb_down' => __("Thumb down", 'municipio'),
          'email' => __("Letter", 'municipio'),
          'phone' => __("Phone", 'municipio'),
          'facebook' => __("Facebook emblem", 'municipio'),
          'chat_bubble' => __("Chat bubble", 'municipio'),
          'close' => __("Close cross", 'municipio'),
          'info' => __("Information", 'municipio'),
        ];
    }

    public function altTextUndefined($altTextUndefined)
    {
        return __("Undefined", 'municipio');
    }
}
