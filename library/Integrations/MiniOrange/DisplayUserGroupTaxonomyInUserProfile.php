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
        if (!method_exists($this->acfService, 'addLocalFieldGroup')) {
            return;
        }

        $this->acfService->addLocalFieldGroup(array(
            'key'                   => 'group_677d3c36476fa',
            'title'                 => $this->wpService->__('User Group', 'municipio'),
            'fields'                => array(
                array(
                    'key'                => 'field_677d3c3942a21',
                    'label'              => $this->wpService->__('User Group', 'municipio'),
                    'name'               => 'user_group',
                    'aria-label'         => '',
                    'type'               => 'acfe_taxonomy_terms',
                    'instructions'       => '',
                    'required'           => 0,
                    'conditional_logic'  => 0,
                    'wrapper'            => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                    'taxonomy'           => array(
                        0 => 'user_group',
                    ),
                    'allow_terms'        => '',
                    'allow_level'        => '',
                    'field_type'         => 'select',
                    'default_value'      => array(),
                    'return_format'      => 'id',
                    'ui'                 => 1,
                    'allow_null'         => 1,
                    'placeholder'        => '',
                    'search_placeholder' => '',
                    'multiple'           => 0,
                    'ajax'               => 1,
                    'save_terms'         => 1,
                    'load_terms'         => 1,
                    'choices'            => array(),
                    'layout'             => '',
                    'toggle'             => 0,
                    'allow_custom'       => 0,
                    'other_choice'       => 0,
                ),
            ),
            'location'              => array(
                array(
                    array(
                        'param'    => 'user_form',
                        'operator' => '==',
                        'value'    => 'all',
                    ),
                ),
            ),
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'left',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => true,
            'description'           => '',
            'show_in_rest'          => 0
        ));
    }
}
