<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Admin;

use AcfService\Contracts\AddLocalFieldGroup;
use WpService\Contracts\AddAction;
use WpService\Contracts\__;

/**
 * Registers the original field and group keys so data created by the former
 * Easy Reading plugin remains available without migration.
 */
final class FieldGroups
{
    public function __construct(
        private AddAction&__ $wpService,
        private AddLocalFieldGroup $acfService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('acf/init', [$this, 'register'], 5);
    }

    public function register(): void
    {
        $this->acfService->addLocalFieldGroup($this->settingsFieldGroup());
        $this->acfService->addLocalFieldGroup($this->contentFieldGroup());
    }

    private function settingsFieldGroup(): array
    {
        return [
            'key' => 'group_58eb9450b0a9f',
            'title' => $this->wpService->__('Easy reading settings', 'municipio'),
            'fields' => [[
                'key' => 'field_58eb9486a647a',
                'label' => $this->wpService->__('Post types', 'municipio'),
                'name' => 'easy_reading_posttypes',
                'type' => 'posttype_select',
                'instructions' => $this->wpService->__('Show easy reading field on selected post types.', 'municipio'),
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => ['width' => '30', 'class' => '', 'id' => ''],
                'default_value' => '',
                'allow_null' => 0,
                'multiple' => 1,
                'placeholder' => '',
                'disabled' => 0,
                'readonly' => 0,
            ]],
            'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'easy-reading-options']]],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => 1,
            'description' => '',
        ];
    }

    private function contentFieldGroup(): array
    {
        return [
            'key' => 'group_58eb4fce51bb7',
            'title' => $this->wpService->__('Easy reading', 'municipio'),
            'fields' => [
                [
                    'key' => 'field_58eb4fe58de9d',
                    'label' => $this->wpService->__('Easy to read content', 'municipio'),
                    'name' => 'easy_reading_select',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
                    'message' => $this->wpService->__('Check this box to add easy to read content version.', 'municipio'),
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ],
                [
                    'key' => 'field_58eb4fed8de9e',
                    'label' => $this->wpService->__('Alternate content', 'municipio'),
                    'name' => 'easy_reading_content',
                    'type' => 'wysiwyg',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => [[['field' => 'field_58eb4fe58de9d', 'operator' => '==', 'value' => '1']]],
                    'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
                    'default_value' => '',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'delay' => 0,
                ],
            ],
            'location' => [[['param' => 'settings', 'operator' => '==', 'value' => '0']]],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => 1,
            'description' => '',
        ];
    }
}
