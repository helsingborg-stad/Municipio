<?php

namespace Municipio\Admin\UI;

use Municipio\Helper\Styleguide;

class Editor
{
    public function __construct()
    {
        // Actions
        add_action('enqueue_block_editor_assets', array($this, 'blockEditorStyle'));
        add_action('enqueue_block_editor_assets', array($this, 'blockEditorScript'));
        add_action('after_setup_theme', array($this, 'editorStyle'));
        add_filter('mce_buttons_2', array($this, 'editorButtons2'));
        add_filter('tiny_mce_before_init', array($this, 'styleFormat'));

        add_action('admin_head', function () {
            echo '<style type="text/css">.acf-postbox table thead th { background-color: #FFF; }</style>';
        });

        // Custom plugins
        $this->metaData();

        // Filters
        add_filter('tiny_mce_before_init', array($this, 'allowedHtmlTags')); // Allow specific html tags for editors
    }

    /**
     * Extend valid html-elements for wp editor
     * @param  array $init
     * @return array
     */
    public function allowedHtmlTags($init)
    {
        $extend = 'div[*], style[*], script[*], iframe[*], span[*], section[*], article[*], header[*], footer[*]';

        if (isset($init['extended_valid_elements']) && !empty($init['extended_valid_elements'])) {
            $init['extended_valid_elements'] .= ', ' . $extend;
        } else {
            $init['extended_valid_elements'] = $extend;
        }

        return $init;
    }

    /**
     * Add styleselect button to editor
     * @param  array $buttons
     * @return array
     */
    public function editorButtons2($buttons)
    {
        array_unshift($buttons, 'styleselect');
        return $buttons;
    }

     /**
     * Add js to block editor
     * @return void
     */
    public function blockEditorScript()
    {
        wp_enqueue_script(
            'columns-block',
            get_template_directory_uri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/blocks/columns.js'),
            array('wp-blocks', 'wp-dom-ready', 'wp-edit-post')
        );
    }

    /**
     * Add stylesheet to block editor
     * @return void
     */
    public function blockEditorStyle()
    {
        global $wp_customize;

        if (isset($wp_customize)) {
            // If in customizer, do not enqueue styles which might affect editor appearance.
            return;
        }

        wp_enqueue_style(
            'block-editor-adjustments',
            apply_filters(
                'Municipio/Admin/BlockEditor',
                get_template_directory_uri() .
                    '/assets/dist/' .
                    \Municipio\Helper\CacheBust::name('css/blockeditor.css')
            )
        );

        wp_enqueue_style(
            'block-editor-styleguide',
            apply_filters(
                'Municipio/Admin/BlockEditorStyleguide',
                get_template_directory_uri() .
                    '/assets/dist/' .
                    \Municipio\Helper\CacheBust::name('css/styleguide.css')
            )
        );

        wp_enqueue_style(
            'block-editor-municipio',
            apply_filters(
                'Municipio/Admin/BlockEditorMunicipio',
                get_template_directory_uri() .
                    '/assets/dist/' .
                    \Municipio\Helper\CacheBust::name('css/municipio.css')
            )
        );
    }

    /**
     * Add stylesheet to TinyMCE editor
     * @return void
     */
    public function editorStyle()
    {

        add_editor_style(
            apply_filters(
                'Municipio/Admin/EditorStyleguide',
                get_template_directory_uri() .
                    '/assets/dist/' .
                    \Municipio\Helper\CacheBust::name('css/styleguide.css')
            )
        );

        add_editor_style(
            apply_filters(
                'Municipio/Admin/EditorTinyMce',
                get_template_directory_uri() .
                    '/assets/dist/' .
                    \Municipio\Helper\CacheBust::name('css/mce.css')
            )
        );
    }
    /**
     * Add style format options
     * @param  array $settings  Options array
     * @return array            Modified options array
     */
    public function styleFormat($settings)
    {
        //Removes h1 from the available element.
        $settings['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Preformatted=pre;';

        // Set color scheme class on mce body
        $color = get_field('color_scheme', 'option');

        if ($color) {
            if (!isset($settings['body_class'])) {
                $settings['body_class'] = '';
            }
            $settings['body_class'] .= ' theme-' . $color;
        }

        // Get style formats
        $styleFormats = self::getEnabledStyleFormats();

        if (empty($styleFormats)) {
            return $settings;
        }

        $settings['style_formats'] = json_encode($styleFormats);
        return $settings;
    }

    /**
     * Get enabled style formats from options
     * @return array
     */
    public static function getEnabledStyleFormats()
    {
        $returnFormats = array();
        $formats       = self::getAvailableStyleFormats();
        $enabled       = get_field('content_editor_formats', 'options');

        if (is_array($enabled) && !empty($enabled) && is_array($formats) && !empty($formats)) {
            foreach ($formats as $key => &$format) {
                $format = array_filter($format, function ($key) use ($enabled) {
                    return in_array($key, $enabled);
                }, ARRAY_FILTER_USE_KEY);
            }

            foreach ($formats as $key => $items) {
                if (!is_array($items) || count($items) === 0) {
                    continue;
                }

                $returnFormats[] = array(
                    'title' => $key,
                    'items' => $items
                );
            }
        }

        return $returnFormats;
    }

    /**
     * Get available style formats for editor
     * @return array
     */
    public static function getAvailableStyleFormats()
    {
        return apply_filters('Municipio\WpEditor\AvailableFormats', array(
            'Text size'      => array(
                'small'       => array(
                    'title'  => 'Small',
                    'inline' => 'small'
                ),
                'large'       => array(
                    'title'   => 'Large',
                    'inline'  => 'span',
                    'classes' => 'text-lg'
                ),
                'extra-large' => array(
                    'title'   => 'Extra large',
                    'inline'  => 'span',
                    'classes' => 'text-xl'
                )
            ),

            'Heading size'   => array(
                'heading-1' => array(
                    'title'   => 'Heading 1',
                    'inline'  => 'span',
                    'classes' => 'h1'
                ),
                'heading-2' => array(
                    'title'   => 'Heading 2',
                    'inline'  => 'span',
                    'classes' => 'h2'
                ),
                'heading-3' => array(
                    'title'   => 'Heading 3',
                    'inline'  => 'span',
                    'classes' => 'h3'
                ),
                'heading-4' => array(
                    'title'   => 'Heading 4',
                    'inline'  => 'span',
                    'classes' => 'h4'
                ),
                'heading-5' => array(
                    'title'   => 'Heading 5',
                    'inline'  => 'span',
                    'classes' => 'h5'
                ),
                'heading-6' => array(
                    'title'   => 'Heading 6',
                    'inline'  => 'span',
                    'classes' => 'h6'
                )
            ),

            'Font weight'    => array(
                'weight-300' => array(
                    'title'   => 'Light (300)',
                    'inline'  => 'span',
                    'classes' => 'weight-300'
                ),
                'weight-400' => array(
                    'title'   => 'Regular (400)',
                    'inline'  => 'span',
                    'classes' => 'weight-400'
                ),
                'weight-500' => array(
                    'title'   => 'Medium (500)',
                    'inline'  => 'span',
                    'classes' => 'weight-500'
                ),
                'weight-700' => array(
                    'title'   => 'Bold (700)',
                    'inline'  => 'span',
                    'classes' => 'weight-700'
                ),
                'weight-900' => array(
                    'title'   => 'Black (900)',
                    'inline'  => 'span',
                    'classes' => 'weight-900'
                )
            ),

            'Text color'     => array(
                'text-color-1' => array(
                    'title'   => 'Color 1',
                    'inline'  => 'span',
                    'classes' => 'text-color-1'
                ),
                'text-color-2' => array(
                    'title'   => 'Color 2',
                    'inline'  => 'span',
                    'classes' => 'text-color-2'
                ),
                'text-color-3' => array(
                    'title'   => 'Color 3',
                    'inline'  => 'span',
                    'classes' => 'text-color-3'
                ),
                'text-color-4' => array(
                    'title'   => 'Color 4',
                    'inline'  => 'span',
                    'classes' => 'text-color-4'
                ),
                'text-color-5' => array(
                    'title'   => 'Color 5',
                    'inline'  => 'span',
                    'classes' => 'text-color-5'
                )
            ),

            'Text transform' => array(
                'uppercase'  => array(
                    'title'   => 'Uppercase',
                    'inline'  => 'span',
                    'classes' => 'text-uppercase'
                ),
                'lowercase'  => array(
                    'title'   => 'Lowercase',
                    'inline'  => 'span',
                    'classes' => 'text-lowercase'
                ),
                'capitalize' => array(
                    'title'   => 'Capitalize',
                    'inline'  => 'span',
                    'classes' => 'text-capitalize'
                )
            ),

            'Highlight'      => array(
                'dark-gray'   => array(
                    'title'   => 'Drak gray',
                    'inline'  => 'span',
                    'classes' => 'text-dark-gray'
                ),
                'highlight'   => array(
                    'title'   => 'Highlight',
                    'inline'  => 'span',
                    'classes' => 'text-highlight'
                ),
                'mark'        => array(
                    'title'   => 'Mark',
                    'inline'  => 'mark',
                    'classes' => 'mark'
                ),
                'mark-yellow' => array(
                    'title'   => 'Mark yellow',
                    'inline'  => 'mark',
                    'classes' => 'mark-yellow'
                ),
                'mark-blue'   => array(
                    'title'   => 'Mark blue',
                    'inline'  => 'mark',
                    'classes' => 'mark-blue'
                ),
                'mark-green'  => array(
                    'title'   => 'Mark green',
                    'inline'  => 'mark',
                    'classes' => 'mark-green'
                ),
                'mark-red'    => array(
                    'title'   => 'Mark red',
                    'inline'  => 'mark',
                    'classes' => 'mark-red'
                ),
                'mark-purple' => array(
                    'title'   => 'Mark purple',
                    'inline'  => 'mark',
                    'classes' => 'mark-purple'
                ),
            )
        ));
    }
}
