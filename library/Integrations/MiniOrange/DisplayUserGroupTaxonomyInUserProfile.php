<?php 

namespace Municipio\Integrations\MiniOrange;

use AcfService\AcfService;
use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig;

class DisplayUserGroupTaxonomyInUserProfile implements Hookable
{
    public function __construct(private WpService $wpService, private AcfService $acfService, private MiniOrangeConfig $config)
    {
    }

    /**
     * Add hooks to register the ACF user group field.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', array($this, 'registerUserGroupField'));
    }

    /**
     * Register the ACF user group field.
     */
    public function registerUserGroupField(): void
    {
        if(!method_exists($this->acfService, 'addLocalFieldGroup')) {
            return; 
        }

        $this->acfService->addLocalFieldGroup(array(
            'key' => 'group_677d3c36476fa',
            'title' => $this->wpService->__('User Group', 'municipio'),
            'fields' => array(
                array(
                    'key' => 'field_677d3c3942a21',
                    'label' => $this->wpService->__('User Group', 'municipio'),
                    'name' => $this->config->getUserGroupTaxonomy(),
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 1,
                    'ui' => 1,
                    'ajax' => 1,
                    'placeholder' => '',
                    'allow_custom' => 0,
                    'search_placeholder' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'user_form',
                        'operator' => '==',
                        'value' => 'all',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'left',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0
        ) );
    }
}