<?php

class acf_field_font_awesome extends acf_field
{
	// vars
	var $settings, // will hold info such as dir / path
		$defaults, // will hold default field options
		$stylesheet, // will hold fontawesome stylesheet url
		$version; // will hold fontawesome version number

	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/

	function __construct()
	{
		$this->name = 'font-awesome';
		$this->label = __('Font Awesome Icon');
		$this->category = __("Content",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'enqueue_fa' 	=>	0,
			'allow_null' 	=>	0,
			'save_format'	=>  'element',
			'default_value'	=>	'',
			'choices'		=>	$this->get_icons()
		);

		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
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
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function create_options($field)
	{
		// defaults?
		$field = array_merge($this->defaults, $field);

		// key is needed in the field names to correctly save the data
		$key = $field['name'];


		// Create Field Options HTML
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default Icon", 'acf'); ?></label>
			</td>
			<td>
				<div class="fa-field-wrapper">
					<div class="fa-live-preview"></div>
					<?php

					do_action('acf/create_field', array(
						'type'    =>  'select',
						'name'    =>  'fields[' . $key . '][default_value]',
						'value'   =>  $field['default_value'],
						'class'	  =>  'fontawesome',
						'choices' =>  array_merge( array( 'null' => __("Select",'acf') ), $field['choices'] )
					));

					?>
				</div>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Return Value",'acf'); ?></label>
				<p class="description"><?php _e("Specify the returned value on front end", 'acf'); ?></p>
			</td>
			<td>
				<?php 
				do_action('acf/create_field', array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][save_format]',
					'value'	=>	$field['save_format'],
					'choices'	=>	array(
						'element'	=>	__("Icon Element",'acf'),
						'class'		=>	__("Icon Class",'acf'),
						'unicode'	=>	__("Icon Unicode",'acf'),
						'object'	=>	__("Icon Object",'acf'),
					),
					'layout'	=>	'horizontal',
				));
				?>
			</td>
		</tr>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Allow Null?",'acf'); ?></label>
			</td>
			<td>
				<?php 
				do_action('acf/create_field', array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][allow_null]',
					'value'	=>	$field['allow_null'],
					'choices'	=>	array(
						1	=>	__("Yes",'acf'),
						0	=>	__("No",'acf'),
					),
					'layout'	=>	'horizontal',
				));
				?>
			</td>
		</tr>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Enqueue FontAwesome?",'acf'); ?></label>
				<p class="description"><?php _e("Set to 'Yes' to enqueue FA in the footer on any pages using this field.", 'acf'); ?></p>
			</td>
			<td>
				<?php 
				do_action('acf/create_field', array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][enqueue_fa]',
					'value'	=>	$field['enqueue_fa'],
					'choices'	=>	array(
						1	=>	__("Yes",'acf'),
						0	=>	__("No",'acf'),
					),
					'layout'	=>	'horizontal',
				));
				?>
			</td>
		</tr>
		<?php

	}

	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function create_field( $field )
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
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// register acf scripts
		wp_enqueue_script('acf-input-font-awesome-select2', $this->settings['dir'] . 'js/select2/select2.min.js', array(), $this->settings['version']);
		wp_enqueue_script('acf-input-font-awesome-edit-input', $this->settings['dir'] . 'js/edit_input.js', array(), $this->settings['version']);
		wp_enqueue_style('acf-input-font-awesome-input', $this->settings['dir'] . 'css/input.css', array(), $this->settings['version']);
		wp_enqueue_style('acf-input-font-awesome-fa', $this->stylesheet, array(), $this->version);
		wp_enqueue_style('acf-input-font-awesome-select2-css', $this->settings['dir'] . 'css/select2.css', array(), $this->settings['version']);
	}

	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add css + javascript to assist your create_field_options() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_enqueue_scripts()
	{
		// register acf scripts
		wp_enqueue_script('font-awesome-select2', $this->settings['dir'] . 'js/select2/select2.min.js', array(), $this->settings['version']);
		wp_enqueue_script('font-awesome-create-input', $this->settings['dir'] . 'js/create_input.js', array(), $this->settings['version']);
		wp_enqueue_style('acf-input-font-awesome-input', $this->settings['dir'] . 'css/input.css', array(), $this->settings['version']);
		wp_enqueue_style('acf-input-font-awesome-fa', $this->stylesheet, array(), $this->version);
		wp_enqueue_style('acf-input-font-awesome-select2-css', $this->settings['dir'] . 'css/select2.css', array(), $this->settings['version']);
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
	*  load_value()
	*
	*  This filter is appied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded from
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in te database
	*/

	function load_value($value, $post_id, $field)
	{
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

}

new acf_field_font_awesome();
