<?php

namespace Municipio\Theme;

class CustomizerFooter extends Customizer
{
    public $footers = array();

    public function __construct($widgetAreas)
    {
        $this->mapFooters($widgetAreas);
        $this->appendItems($widgetAreas);

        foreach ($this->footers as $footer) {
            $this->footerClasses($footer);
            $this->itemClasses($footer['items'], $footer['id']);
        }

        $this->footers = apply_filters('Municipio/Theme/CustomizerFooter/Footers', $this->footers);
    }

    public function itemClasses($items, $footerId)
    {
        if (!is_array($items) || empty($items)) {
            return;
        }

        foreach ($items as $i => $item) {
            $itemClasses = array();
            $itemClasses[] = 'c-footer__item';
            $itemClasses[] = 'c-footer__item--' . $item['alignment'];

            $itemClasses = apply_filters('Municipio/Theme/CustomizerFooter/itemClasses', $itemClasses, $footerId);

            $items[$i]['classes'] = $itemClasses;
            $items[$i]['class'] = implode(' ', $itemClasses);
        }

        $this->footers[$footerId]['items'] = $items;
    }

    public function footerClasses($footer)
    {
        $footerClasses = array();
        $footerClasses[] = 'c-footer';
        $footerClasses[] = 'c-footer--' . $footer['id'];
        $footerClasses[] = 'c-footer--customizer';
        $footerClasses[] = MUNICIPIO_BEM_THEME_NAME;

        $footerClasses = apply_filters('Municipio/Theme/CustomizerFooter/footerClasses', $footerClasses, $footer);

        $rowClasses = array();
        $rowClasses[] = 'c-footer__row';
        $rowClasses[] = 'container';

        $rowClasses = apply_filters('Municipio/Theme/CustomizerFooter/rowClasses', $rowClasses, $footer);

        $footer['classes'] = $footerClasses;
        $footer['class'] = implode(' ', $footerClasses);
        $footer['rowClasses'] = $rowClasses;
        $footer['rowClass'] = implode(' ', $rowClasses);

        $this->footers[$footer['id']] = $footer;
    }

    public function appendItems($widgetAreas)
    {
        foreach ($widgetAreas as $widgetArea) {
            $this->footers[$widgetArea['position']]['items'][] = $widgetArea;
        }
    }

    public function mapFooters($widgetAreas)
    {
        $enabledFooters = array();

        foreach ($widgetAreas as $widgetArea) {
            $enabledFooters[] = $widgetArea['position'];
        }

        $enabledFooters = array_unique($enabledFooters);
        $footers = array();

        foreach ($enabledFooters as $footer) {
            $footers[$footer] = array(
                'id'        => $footer,
                'name'      => ucfirst($footer),
                'classes'   => array(),
                'class'     => '',
                'rowClasses' => array(),
                'rowClass'  => '',
                'items'     => array()
            );
        }

        $this->footers = $footers;
    }
}
