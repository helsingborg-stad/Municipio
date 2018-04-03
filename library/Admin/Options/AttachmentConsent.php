<?php

namespace Municipio\Admin\Options;

class AttachmentConsent
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'attachmentConsentField'));
    }

    public function attachmentConsentField()
    {
        if (!get_field('gdpr_enable_attachment_consent_field', 'options') || !function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group(array(
            'key' => 'group_5ac3358232ab5',
            'title' => 'Attachment - Consent agreement',
            'fields' => array(
                array(
                    'key' => 'field_5ac3358e33850',
                    'label' => __('Consent ID', 'municipio'),
                    'name' => 'attachment_consent_id',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
                array(
                    'key' => 'field_5ac33bad94a8a',
                    'label' => __('Consent comment', 'municipio'),
                    'name' => 'attachment_consent_comment',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'maxlength' => '',
                    'rows' => 3,
                    'new_lines' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'attachment',
                        'operator' => '==',
                        'value' => 'image',
                    ),
                ),
                array(
                    array(
                        'param' => 'attachment',
                        'operator' => '==',
                        'value' => 'video',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => 1,
            'description' => '',
        ));
    }
}
