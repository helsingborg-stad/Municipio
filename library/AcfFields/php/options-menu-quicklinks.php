<?php


if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_6123844e04276',
	'title' => 'Quicklinks menu',
	'fields' => array(
		array(
			'key' => 'field_6123844e0f0bb',
			'label' => 'Background Color',
			'name' => 'quicklinks_background_color',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'white' => 'White (transparent)',
				'primary' => 'Primary',
				'secondary' => 'Secondary',
				'tertiary' => 'Tertiary',
			),
			'default_value' => 'white',
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'return_format' => 'value',
			'ajax' => 0,
			'placeholder' => '',
		),
		array(
			'key' => 'field_6127571bcc76e',
			'label' => 'Text Color',
			'name' => 'quicklinks_text_color',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'white' => 'White',
				'black' => 'Black',
			),
			'default_value' => 'black',
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'return_format' => 'value',
			'ajax' => 0,
			'placeholder' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'nav_menu',
				'operator' => '==',
				'value' => 'location/quicklinks-menu',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

endif;