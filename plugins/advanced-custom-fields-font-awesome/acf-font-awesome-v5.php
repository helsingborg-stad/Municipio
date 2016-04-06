<?php

class acf_field_font_awesome extends acf_field {
	
	var	$stylesheet, // will hold fontawesome stylesheet url
		$version; // will hold fontawesome version number

	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {	

		$this->name = 'font-awesome';
		$this->label = __('Font Awesome Icon');
		$this->category = __("Content",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'enqueue_fa' 	=>	0,
			'allow_null' 	=>	0,
			'save_format'	=>  'element',
			'default_value'	=>	'',
			'fa_live_preview'	=>	'',
			'choices'		=>	$this->get_icons()
		);
		$this->l10n = array();

		$this->settings = array(
			'path' => dirname(__FILE__),
			'dir' => $this->helpers_get_dir( __FILE__ ),
			'version' => '1.5'
		);

		add_filter('acf/load_field', array( $this, 'maybe_enqueue_font_awesome' ) );

		parent::__construct();
	}

	function get_icons()
	{
		require_once ( dirname( __FILE__ ) . '/better-font-awesome-library/better-font-awesome-library.php' );

		$args = array(
			'version'				=> 'latest',
			'minified'				=> true,
			'remove_existing_fa'	=> false,
			'load_styles'			=> false,
			'load_admin_styles'		=> false,
			'load_shortcode'		=> false,
			'load_tinymce_plugin'	=> false
		);

		$bfa 		= Better_Font_Awesome_Library::get_instance( $args );
		$bfa_icons	= $bfa->get_icons();
		$bfa_prefix	= $bfa->get_prefix() . '-';
		$new_icons	= array();

		$this->stylesheet	= $bfa->get_stylesheet_url();
		$this->version		= $bfa->get_version();

		foreach ( $bfa_icons as $hex => $class ) {
			$unicode = '&#x' . ltrim( $hex, '\\') . ';';
			$new_icons[ $bfa_prefix . $class ] = $unicode . ' ' . $bfa_prefix . $class;
		}

		$new_icons = array_merge( array( 'null' => '- Select -' ), $new_icons );

		return $new_icons;
	}

	/*
	*  maybe_enqueue_font_awesome()
	*
	*  If Enqueue FA is set to true, enqueue it in the footer. We cannot enqueue in the header because wp_head has already been called
	*  
	*/

	function maybe_enqueue_font_awesome( $field )
	{
		if( 'font-awesome' == $field['type'] && $field['enqueue_fa'] ) {
			add_action( 'wp_footer', array( $this, 'frontend_enqueue_scripts' ) );
		}

		return $field;
	}

	/*
	*  frontend_enqueue_scripts()
	*
	*  This action is called in the wp_enqueue_scripts action on the front end.
	*  
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
	*  @type	action
	*/

	function frontend_enqueue_scripts()
	{
		wp_register_style('font-awesome', $this->stylesheet, array(), $this->version);

		wp_enqueue_style( array( 'font-awesome' ) );
	}

	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field_settings( $field )
	{
		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Live Preview','acf-font-awesome'),
			'instructions'	=> '',
			'type'			=> 'message',
			'name'			=> 'fa_live_preview',
			'class'			=> 'live-preview'
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Default Icon','acf-font-awesome'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'default_value',
			'class'	  		=>  'fontawesome',
			'choices'		=>	$field['choices']
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Return Value','acf-font-awesome'),
			'instructions'	=> __('Specify the returned value on front end','acf-font-awesome'),
			'type'			=> 'radio',
			'name'			=> 'save_format',
			'choices'	=>	array(
				'element'	=>	__('Icon Element','acf-font-awesome'),
				'class'		=>	__('Icon Class','acf-font-awesome'),
				'unicode'	=>	__('Icon Unicode','acf-font-awesome'),
				'object'	=>	__('Icon Object','acf-font-awesome'),
			)
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Allow Null?','acf-font-awesome'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'allow_null',
			'choices'	=>	array(
				1	=>	__('Yes','acf-font-awesome'),
				0	=>	__('No','acf-font-awesome')
			)
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Enqueue FontAwesome?','acf-font-awesome'),
			'instructions'	=> __('Set to \'Yes\' to enqueue FA in the footer on any pages using this field.','acf-font-awesome'),
			'type'			=> 'radio',
			'name'			=> 'enqueue_fa',
			'choices'	=>	array(
				1	=>	__('Yes','acf-font-awesome'),
				0	=>	__('No','acf-font-awesome')
			)
		));
	}
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field )
	{
		if( 'object' == $field['save_format'] && 'null' !== $field['value'] )
			$field['value'] = array( $field['value']->class );

		// value must be array
		if( !is_array($field['value']) )
		{
			// perhaps this is a default value with new lines in it?
			if( strpos($field['value'], "\n") !== false )
			{
				// found multiple lines, explode it
				$field['value'] = explode("\n", $field['value']);
			}
			else
			{
				$field['value'] = array( $field['value'] );
			}
		}
		
		// trim value
		$field['value'] = array_map('trim', $field['value']);
		
		// html
		echo '<div class="fa-field-wrapper">';
		echo '<div class="fa-live-preview"></div>';
		echo '<select id="' . $field['id'] . '" class="' . $field['class'] . ' fa-select2-field" name="' . $field['name'] . '" >';	
		
		// null
		if( $field['allow_null'] )
		{
			echo '<option value="null">- ' . __("Select",'acf') . ' -</option>';
		}
		
		// loop through values and add them as options
		if( is_array($field['choices']) )
		{
			unset( $field['choices']['null'] );

			foreach( $field['choices'] as $key => $value )
			{
				$selected = $this->find_selected( $key, $field['value'], $field['save_format'], $field['choices'] );
				echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}
		}

		echo '</select>';
		echo '</div>';
	}

	function find_selected( $needle, $haystack, $type, $choices )
	{
		switch( $type )
		{
			case 'object':
			case 'element':
				$search = array( '<i class="fa ', '"></i>' );
				$string = str_replace( $search, '', $haystack[0] );
				break;

			case 'unicode':
				$index = $choices[ $needle ];
				if ( stristr( $index, $haystack[0] ) ) {
					return 'selected="selected"';
				}
				return '';

			case 'class':
				$string = $haystack[0];
				break;
		}

		if( $string == $needle )
			return 'selected="selected"';

		return '';
	}
		
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function input_admin_enqueue_scripts() {

		// register acf scripts
		wp_enqueue_script('acf-input-font-awesome-edit-input', $this->settings['dir'] . 'js/edit_input.js', array(), $this->settings['version']);
		wp_enqueue_style('acf-input-font-awesome-input', $this->settings['dir'] . 'css/input.css', array(), $this->settings['version']);
		wp_enqueue_style('acf-input-font-awesome-fa', $this->stylesheet, array(), $this->version);
	}
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function field_group_admin_enqueue_scripts() {

		// register acf scripts
		wp_enqueue_script('font-awesome-create-input', $this->settings['dir'] . 'js/create_input.js', array(), $this->settings['version']);
		wp_enqueue_style('acf-input-font-awesome-input', $this->settings['dir'] . 'css/input.css', array(), $this->settings['version']);
		wp_enqueue_style('acf-input-font-awesome-fa', $this->stylesheet, array(), $this->version);
	}
	
	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	function load_value( $value, $post_id, $field ) {

		if ( 'null' == $value ) {
			return;
		}

		switch( $field['save_format'] )
		{
			case 'object':
				$icon_unicode_string = $this->defaults['choices'][ $value ];
				$icon_unicode_arr = explode( ' ', $icon_unicode_string );
				$icon_unicode = $icon_unicode_arr[0];
				$value = (object) array(
						'unicode' => $icon_unicode,
						'class'	  => $value,
						'element' => '<i class="fa ' . $value . '"></i>'
					);
				break;

			case 'unicode':
				$icon_unicode_string = $this->defaults['choices'][ $value ];
				$icon_unicode_arr = explode( ' ', $icon_unicode_string );
				$value = $icon_unicode_arr[0];
				break;

			case 'element':
				$value = '<i class="fa ' . $value . '"></i>';
				break;
		}

		return $value;
	}

	/*
	*  helpers_get_dir()
	*
	*  Helper function taken from ACF 4.x to allow finding of asset paths when plugin is included from outside the plugins directory
	*
	*/

	function helpers_get_dir( $file ) {
		
		$dir = trailingslashit( dirname( $file ) );
		$count = 0;

		// sanitize for Win32 installs
		$dir = str_replace('\\' ,'/', $dir); 
		
		// if file is in plugins folder
		$wp_plugin_dir = str_replace( '\\' ,'/', WP_PLUGIN_DIR ); 
		$dir = str_replace( $wp_plugin_dir, plugins_url(), $dir, $count );

		if ( $count < 1 ) {
			// if file is in wp-content folder
			$wp_content_dir = str_replace( '\\' ,'/', WP_CONTENT_DIR ); 
			$dir = str_replace( $wp_content_dir, content_url(), $dir, $count );
		}

		if ( $count < 1 ) {
			// if file is in ??? folder
			$wp_dir = str_replace( '\\' ,'/', ABSPATH ); 
			$dir = str_replace( $wp_dir, site_url( '/' ), $dir );
		}
		
		return $dir;
	}

}

// create field
new acf_field_font_awesome();
