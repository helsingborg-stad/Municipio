<?php

namespace Municipio\Customizer;

use Municipio\Customizer\Source\CustomizerRepeaterInput as CustomizerRepeaterInput;
use Municipio\Customizer\Source\CustomizerClass as CustomizerClass;
use Municipio\Customizer\Source\CustomizerHelper as CustomizerHelper;

class Customizer extends CustomizerHelper
{
    public $config = 'municipio_config';

    public $sidebarSections = array();

    public function __construct()
    {
        add_filter('Municipio/Customizer/Source/CustomizerRepeaterInput', array($this, 'filterHeaderInput'), 5, 4);
        add_filter('Municipio/Customizer/Source/CustomizerRepeaterInput', array($this, 'filterFooterInput'), 5, 4);
        add_filter('kirki/config', array($this, 'krikiPath'));
        add_action('init', array($this, 'customizerConfig'));
        add_action('init', array($this, 'customizerHeader'));
        add_action('init', array($this, 'customizerFooter'));
    }

    public function customizerHeader()
    {
        $headers = $this->getRepeaterField('customizer__header_sections');

        if (!$headers->hasItems()) {
            return;
        }

        $customizer = new CustomizerClass($this->config);
        $sidebarHelper = $customizer->sidebarHelper();

        //Create panel
        $headerPanel = $customizer->createPanel('municipio-panel-header', 'Header', 'Header settings', 60);

        //Repeater loop
        foreach ($headers->repeater as $header) {
            $sidebarId = $header['sidebars'][0];

            //Modify sidebar args
            $sidebarArgs = array_merge($sidebarHelper->getArgs(), [
                'before_widget' => '<div class="grid-xs-auto c-header__item widget %2$s">'
            ]);
            $sidebarHelper->mergeArgs($sidebarArgs);

            //Register sidebar
            $sidebarHelper->registerSidebar($sidebarId, $header['name']);

            //Move sidebar sections to panel in customizer preview
            $headerPanel->moveSidebarIntoPanel($sidebarId, $customizer->getSidebarSectionId($sidebarId));

            //Add fields to sidebar section
            $sidebarSection = $customizer->getSidebarSection($sidebarId);
            $sidebarSection->keyPrefix = $sidebarId . '__';
            $sidebarSection->commonHeaderFields('.c-header--customizer.' . $header['id']);
        }
    }

    public function customizerFooter()
    {
        $footers = $this->getRepeaterField('customizer__footer_sections');

        if (!$footers->hasItems()) {
            return;
        }

        $customizer = new CustomizerClass($this->config);
        $sidebarHelper = $customizer->sidebarHelper();

        //Create panel
        $footerPanel = $customizer->createPanel('municipio-panel-footer', 'Footer', 'Footer settings', 60);

        //Repeater loop
        foreach ($footers->repeater as $footer) {
            //Settings section
            $footerSettingsSection = $customizer->createSection($footer['id'] . '-settings', $footer['name'] . ' - settings', 'Some description', 100, $footerPanel->getPanel());
            $footerSettingsSection->keyPrefix = $footer['id'] . '__';
            $footerSettingsSection->commonFooterFields('.c-footer.c-footer--customizer.' . $footer['id']);

            //Sidebar loop
            $i = 1;
            foreach ($footer['sidebars'] as $sidebar) {
                //Register sidebar
                $sidebarHelper->registerSidebar($sidebar, $footer['name'] . ' column ' . $i);

                //Move sidebar sections to panel in customizer preview
                $footerPanel->moveSidebarIntoPanel($sidebar, $customizer->getSidebarSectionId($sidebar));

                //Sidebar fields
                $sidebarSection = $customizer->getSidebarSection($sidebar);
                $sidebarSection->keyPrefix = $sidebar . '__';
                $sidebarSection->commonFooterColumnFields();

                $i++;
            }
        }
    }

    public function filterHeaderInput($repeater, $field, $fieldId, $identifierKey)
    {
        if ($field != 'customizer__header_sections') {
            return $repeater;
        }

        $name = str_replace('header', '', $repeater['name']);
        $id = str_replace('header', '', $repeater['id']);

        $mappedData = array(
            'id' => sprintf('customizer-header-%s', $id),
            'name' => sprintf('Header: %s', ucfirst($name))
        );

        $mappedData['sidebars'][] = $mappedData['id'];

        return array_merge($repeater, $mappedData);
    }

    public function filterFooterInput($repeater, $field, $fieldId, $identifierKey)
    {
        if ($field != 'customizer__footer_sections') {
            return $repeater;
        }

        $name = str_replace('footer', '', $repeater['name']);
        $id = str_replace('footer', '', $repeater['id']);

        $mappedData = array(
            'id' => sprintf('customizer-footer-%s', $id),
            'name' => sprintf('Footer: %s', ucfirst($name)),
            'sidebars' => $this->generateIds('customizer-footer-'. $id . '-column-%d', $repeater['columns'])
        );

        return array_merge($repeater, $mappedData);
    }


    public function customizerConfig()
    {
        $this->addConfig($this->config, 'edit_theme_options', 'theme_mod');
    }

    public function addConfig($key, $capability, $optionsType)
    {
        \Kirki::add_config($key, array(
            'capability'    => $capability,
            'option_type'   => $optionsType,
        ));
    }

    /**
     * Make sure child theme get correct kirkiPath
     * @param  array $config Kirki config
     * @return array
     */
    public function krikiPath($config)
    {
        if (!is_array($config)) {
            $config = array();
        }

        $config['url_path'] = get_template_directory_uri() . '/vendor/aristath/kirki/';

        return $config;
    }

    public function getRepeaterField($field)
    {
        return new CustomizerRepeaterInput($field, 'options', 'id');
    }
}
