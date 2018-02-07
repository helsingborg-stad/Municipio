<?php

namespace Municipio\Theme;

class CustomizerHeader extends Customizer
{
    public $headers = array();

    public function __construct($widgetAreas)
    {
        $this->mapHeaders($widgetAreas);
        $this->appendItems($widgetAreas);

        foreach ($this->headers as $header) {
            $this->headerClasses($header);
            $this->itemClasses($header['items'], $header['id']);
        }

        $this->headers = apply_filters('Municipio/Theme/CustomizerHeader/Headers', $this->headers);
    }

    public function itemClasses($items, $headerId)
    {
        if (!is_array($items) || empty($items)) {
            return;
        }

        foreach ($items as $i => $item) {
            $itemClasses = array();
            $itemClasses[] = 'c-header__item';
            $itemClasses[] = 'c-header__item--' . $item['alignment'];

            $itemClasses = apply_filters('Municipio/Theme/CustomizerHeader/itemClasses', $itemClasses, $headerId);

            $items[$i]['classes'] = $itemClasses;
            $items[$i]['class'] = implode(' ', $itemClasses);
        }

        $this->headers[$headerId]['items'] = $items;
    }

    public function headerClasses($header)
    {
        $headerClasses = array();
        $headerClasses[] = 'c-header';
        $headerClasses[] = 'c-header--' . $header['id'];
        $headerClasses[] = 'c-header--customizer';
        $headerClasses[] = 't-' . $this->getThemeKey(false);

        if (is_child_theme()) {
            $headerClasses[] = 't-' . $this->getThemeKey(true);
        }

        $headerClasses = apply_filters('Municipio/Theme/CustomizerHeader/headerClasses', $headerClasses, $header);

        $rowClasses = array();
        $rowClasses[] = 'c-header__row';
        $rowClasses[] = 'container';

        $rowClasses = apply_filters('Municipio/Theme/CustomizerHeader/rowClasses', $rowClasses, $header);

        $header['classes'] = $headerClasses;
        $header['class'] = implode(' ', $headerClasses);
        $header['rowClasses'] = $rowClasses;
        $header['rowClass'] = implode(' ', $rowClasses);

        $this->headers[$header['id']] = $header;
    }

    public function appendItems($widgetAreas)
    {
        foreach ($widgetAreas as $widgetArea) {
            $this->headers[$widgetArea['position']]['items'][] = $widgetArea;
        }
    }

    public function mapHeaders($widgetAreas)
    {
        $enabledHeaders = array();

        foreach ($widgetAreas as $widgetArea) {
            $enabledHeaders[] = $widgetArea['position'];
        }

        $enabledHeaders = array_unique($enabledHeaders);
        $headers = array();

        foreach ($enabledHeaders as $header) {
            $headers[$header] = array(
                'id'        => $header,
                'name'      => ucfirst($header),
                'classes'   => array(),
                'class'     => '',
                'rowClasses' => array(),
                'rowClass'  => '',
                'items'     => array()
            );
        }

        $this->headers = $headers;
    }
}
