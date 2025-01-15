<?php

namespace Municipio\UserGroup;

use AcfService\AcfService;
use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\UserGroup\Config\UserGroupConfigInterface;

/**
 * Display User Group taxonomy in user profile.
 */
class DisplayUserGroupTaxonomyInUserProfile implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private WpService $wpService, private AcfService $acfService, private UserGroupConfigInterface $config)
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
                    'key'                  => 'field_677d3c3942a21',
                    'label'                => $this->wpService->__('User Group', 'municipio'),
                    'name'                 => 'user_group',
                    'aria-label'           => '',
                    'type'                 => 'taxonomy',
                    'instructions'         => '',
                    'required'             => 0,
                    'conditional_logic'    => 0,
                    'wrapper'              => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                    'taxonomy'             => 'user_group',
                    'add_term'             => 0,
                    'save_terms'           => 1,
                    'load_terms'           => 1,
                    'return_format'        => 'id',
                    'field_type'           => 'select',
                    'allow_null'           => 1,
                    'acfe_bidirectional'   => array(
                        'acfe_bidirectional_enabled' => '0',
                    ),
                    'bidirectional'        => 0,
                    'multiple'             => 0,
                    'bidirectional_target' => array(
                    ),
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
