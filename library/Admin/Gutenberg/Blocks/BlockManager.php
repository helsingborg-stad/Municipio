<?php

namespace Municipio\Admin\Gutenberg\Blocks;

use Municipio\Helper\ColorSwatches;

/**
 * Class BlockManager
 *
 * This class manages the registration of blocks.
 */
class BlockManager
{
    /* These block works fine without validation */
    private $noValidationRequired = [
        'acf/button',
        'acf/innerbutton',
    ];

    /**
     * Class constructor.
     */
    public function __construct()
    {
        add_filter('Municipio/blade/view_paths', array($this, 'getViewPath'), 10);
        add_action('init', array($this, 'registerBlocks'), 10);
    }

    /**
     * Register blocks
     *
     * @return void
     */
    public function registerBlocks()
    {
        // Check function exists.
        if (function_exists('acf_register_block_type')) {
            // register a button block.
            acf_register_block_type(array(
                'name'            => 'button',
                'title'           => __('Button', 'municipio'),
                'description'     => __('A button block', 'municipio'),
                'render_callback' => array($this, 'renderCallback'),
                'category'        => 'design',
                'icon'            => 'button',
                'keywords'        => array('button', 'link'),
                'supports'        => [
                    'align'  => true,
                    'jsx'    => true,
                    'anchor' => true,
                ],
                'view'            => 'button'
            ));

            // register a button block (inner).
            acf_register_block_type(array(
                'name'            => 'innerButton',
                'title'           => __('Button (Inner)', 'municipio'),
                'description'     => __('A button block', 'municipio'),
                'render_callback' => array($this, 'renderCallback'),
                'category'        => 'design',
                'icon'            => 'design',
                'keywords'        => array('button', 'link'),
                'parent'          => ['acf/button'],
                'supports'        => [
                    'align'  => false,
                    'jsx'    => true,
                    'anchor' => true,
                ],
                'view'            => 'button'
            ));

            acf_register_block_type(array(
                'name'            => 'classic',
                'title'           => __('Classic', 'municipio'),
                'description'     => __('A block that lets you create and edit articles', 'municipio'),
                'render_callback' => array($this, 'renderCallback'),
                'category'        => 'text',
                'icon'            => 'text',
                'keywords'        => array('editor', 'classic'),
                'supports'        => [
                    'align'  => true,
                    'anchor' => true,
                ],
                'view'            => 'classic'
            ));

            // register a margin block.
            acf_register_block_type(array(
                'name'            => 'margin',
                'title'           => __('Margin', 'municipio'),
                'description'     => __('A margin block', 'municipio'),
                'render_callback' => array($this, 'renderCallback'),
                'category'        => 'design',
                'icon'            => 'fullscreen-exit-alt',
                'keywords'        => array('margin', 'space', 'whitespace', 'padding', 'air'),
                'supports'        => [
                    'align'  => true,
                    'jsx'    => true,
                    'anchor' => true,
                ],
                'view'            => 'margin'
            ));

            // register a container block.
            acf_register_block_type(array(
                'name'            => 'container',
                'title'           => __('Container', 'municipio'),
                'description'     => __('A container block', 'municipio'),
                'render_callback' => array($this, 'renderContainerCallback'),
                'category'        => 'design',
                'icon'            => 'archive',
                'keywords'        => array('container', 'wrapper', 'background'),
                'supports'        => [
                    'align'  => true,
                    'jsx'    => true,
                    'anchor' => true,
                ],
                'view'            => 'container'
            ));
        }
    }

    /**
     * Callback for block, renders view.
     *
     * @param array $block
     * @return void
     */
    public function renderCallback($block)
    {
        $data              = $this->buildData($block['data']);
        $data['blockType'] = $block['name'];
        $data['classList'] = $this->buildBlockClassList($block);
        $data['anchor']    = $block['anchor'] ?? '';

        if ($this->validateFields($block['data']) || in_array($block['name'], $this->noValidationRequired)) {
            echo render_blade_view($block['view'], $data);
        } else {
            echo render_blade_view('default', ['blockTitle' => $block['title'], 'message' => __('Please fill in all required fields.', 'municipio')]);
        }
    }
    /**
     * Callback for container block, renders view.
     *
     * @param array $block
     * @return void
     */
    public function renderContainerCallback($block)
    {
        $data = $this->buildData($block['data']);

        $data['blockType'] = $block['name'];
        $data['classList'] = $this->buildBlockClassList($block);

        $data['contentClassList'] = 'o-container o-container--remove-spacing';
        if (isset($data['content_width']) && $data['content_width'] == 'article') {
            $data['contentClassList'] .= ' c-article c-article--readable-width';
        }

        if ($this->validateFields($block['data']) || in_array($block['name'], $this->noValidationRequired)) {
            $data['style'] = [];

            $backgroundClass = is_string($data['color'] ?? null) ? ColorSwatches::getColorUtilityClass($data['color']) : null;
            if ($backgroundClass !== null && isset($data['background_color_type']) && $data['background_color_type'] != 'gradient') {
                $data['classList'] .= ' ' . $backgroundClass;
            }

            if ($this->blockHasBackgroundGradient($data)) {
                $gradientRole = $this->getGradientContrastRole($data);
                if ($gradientRole !== null) {
                    $data['classList'] .= ' u-color__text--' . $gradientRole . '-contrast';
                }
            }

            if ($this->blockHasBackgroundGradient($data)) {
                $data['style'][] = $this->getBlockBackgroundGradientStyles($data);
            }

            // Padding amount
            if (!empty($data['amount'])) {
                $data['halfAmount'] = ceil($data['amount'] / 2);
                $data['classList'] .= ' u-padding--' . $data['amount'];
                $data['classList'] .= ' u-padding--' . $data['halfAmount'] . '@md';
                $data['classList'] .= ' u-padding__x--2@xs';
                $data['classList'] .= ' u-padding__x--2@sm';
            }

            if (!empty($data['backgroundImage'])) {
                $image = wp_get_attachment_image_url($data['backgroundImage'], 'full');
                if ($image) {
                    $data['style'][] = "background-image:url($image)";
                    $data['style'][] = "background-size:cover";
                    $data['style'][] = "background-position:center center";
                }
            }

            // Border radius
            if (!empty($data['border_radius'])) {
                $borderRadiusMap    = ['sm' => 2, 'md' => 4, 'lg' => 8, 'xl' => 16];
                $borderRadius       = $borderRadiusMap[$data['border_radius']] ?? 0;
                $data['classList'] .= ' u-rounded--' . $borderRadius;
            }

            // Shadow
            if (!empty($data['shadow'])) {
                $data['classList'] .= ' u-box-shadow--' . $data['shadow'];
            }

            if (!empty($data['style'])) {
                $data['style'] = implode(';', $data['style']);
            } else {
                $data['style'] = '';
            }

            $data['anchor'] = $block['anchor'] ?? '';
            if (!empty($data['anchor'])) {
                if (empty($data['attributeList'])) {
                    $data['attributeList'] = [];
                }
                if (!empty($data['anchor'])) {
                    $data['attributeList']['id'] = $data['anchor'];
                }
            }

            echo render_blade_view(
                $block['view'],
                $data
            );
        } else {
            echo render_blade_view(
                'default',
                [
                    'blockTitle' => $block['title'],
                    'message'    => __('Please fill in all required fields.', 'municipio')
                ]
            );
        }
    }

    /**
     * Check if the block has a background gradient.
     *
     * @param array $data The block data.
     * @return bool
     */
    private function blockHasBackgroundGradient($data): bool
    {
        return !empty($data['background_gradient']) && $data['background_color_type'] == 'gradient';
    }

    /**
     * Get the styles for the block background gradient.
     *
     * @param array $data The block data.
     * @return string The generated CSS styles.
     */
    private function getBlockBackgroundGradientStyles($data): string
    {
        $styles         = "";
        $gradientValues = "";
        $gradientStops    = $data['background_gradient'];
        $angle          = $data['background_gradient_angle'] ?? '0';
        $type           = $data['background_gradient_type'];

        if ($type == 'advanced') {
            usort($gradientStops, function ($firstGradientStop, $secondGradientStop) {
                return $firstGradientStop['stop'] <=> $secondGradientStop['stop'];
            });
        }

        foreach ($gradientStops as $key => $gradient) {
            $colorPair = is_string($gradient['color'] ?? null) ? ColorSwatches::getColorPair($gradient['color']) : null;
            $gradientValues .= ($colorPair['background'] ?? 'transparent') . $this->handleGradientStopValues($gradient, $type);

            if ($key !== array_key_last($gradientStops)) {
                $gradientValues .= ', ';
            }
        }

        // CSS gradients require at least two stops. Keep a newly added single
        // colour visible while the editor is still configuring the gradient.
        if (count($gradientStops) === 1) {
            $gradientValues .= ', ' . $gradientValues;
        }

        if (!empty($gradientValues)) {
            $styles = "background: linear-gradient({$angle}deg, $gradientValues);";
        }

        return $styles;
    }

    /**
     * Uses the first visible gradient colour's paired contrast colour.
     *
     * @param array<string, mixed> $data The block data.
     * @return string|null The semantic palette role.
     */
    private function getGradientContrastRole(array $data): ?string
    {
        $gradientStops = $data['background_gradient'] ?? null;
        if (!is_array($gradientStops)) {
            return null;
        }

        if (($data['background_gradient_type'] ?? null) === 'advanced') {
            usort($gradientStops, fn ($firstGradientStop, $secondGradientStop) => ($firstGradientStop['stop'] ?? 0) <=> ($secondGradientStop['stop'] ?? 0));
        }

        foreach ($gradientStops as $gradient) {
            if (!is_array($gradient) || empty($gradient['color'])) {
                continue;
            }

            if (is_string($gradient['color']) && ColorSwatches::getColorPair($gradient['color']) !== null) {
                return $gradient['color'];
            }
        }

        return null;
    }

    /**
     * Handle the gradient stop values.
     *
     * @param array $gradient The gradient data.
     * @param string $type The type of gradient.
     * @return string The generated stop value.
     */
    private function handleGradientStopValues($gradient, $type)
    {
        if ($type == 'advanced') {
            return ' ' . $gradient['stop'] . '%';
        }

        return '';
    }

    /**
     * Return the general view path
     *
     * @param array $paths
     * @return array
     */
    public function getViewPath($paths)
    {
        $paths[] = plugin_dir_path(__FILE__) . 'views';
        return $paths;
    }

    /**
     * Builds data to view.
     *
     * @param array $data
     * @return array
     */
    public function buildData($data)
    {
        $newData = [];
        foreach ($data as $key => $value) {
            // Unsaved editor data uses field keys and nested repeater arrays.
            // Read through ACF so subfields are formatted to their field names.
            if (str_starts_with($key, 'field_')) {
                $fieldObject = get_field_object($key);
                if ($fieldObject !== false) {
                    $newData[$fieldObject['name']] = get_field($key);
                }
                continue;
            }

            $key = ltrim($key, '_');

            if (is_string($value) && str_starts_with($value, 'field_')) {
                $newData[$key] = get_field($value);
            } else {
                if ($fieldObject = get_field_object($key)) {
                    $newData[$fieldObject['name']] = $value;
                }
            }
        }

        return $newData;
    }

    /**
     * Build block classlist
     *
     * @param [type] $block
     * @return void
     */
    public function buildBlockClassList($block)
    {
        $classList = ['t-block-container'];

        if (in_array($block['name'], ['acf/button'])) {
            $classList[] = "t-block-button";
        }

        if (isset($block['align']) && !empty($block['align'])) {
            $classList[] = "t-block-align-" . $block['align'];
        }

        // Add WordPress' extra classes
        if (isset($block['className']) && !empty($block['className'])) {
            $classList[] = $block['className'];
        }

        return implode(' ', $classList);
    }

    /**
     * Validates the required fields
     * @return boolean
     */
    private function validateFields($fields)
    {

        $valid = true;

        foreach ($fields as $key => $value) {
            //Must validate as a field_key
            $fieldKey = null;
            if (str_starts_with($key, 'field_')) {
                $fieldKey = $key;
            } elseif (is_string($value) && str_starts_with($value, 'field_')) {
                $fieldKey = $value;
            }
            if ($fieldKey === null) {
                continue;
            }

            // Get full field specification
            if (!$fieldObject = get_field_object($fieldKey)) {
                continue;
            }

            //Skip validation of decendants
            if (isset($fieldObject['parent']) && str_contains($fieldObject['parent'], 'field_')) {
                continue;
            }

            //Check if required field has a value
            if ($fieldObject['required'] && (!$fieldObject['value'] && $fieldObject['value'] !== "0")) {
                $valid = false;
            }
        }

        return $valid;
    }
}
