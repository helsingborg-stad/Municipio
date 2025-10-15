<?php

namespace Modularity;

class BlockManager
{
    public $modules = [];
    public $classes = [];

    public function __construct()
    {
        add_filter('block_categories_all', array($this, 'filterCategories'), 10, 2);
        add_filter('acf/load_field_group', array($this, 'addLocationRule'));

        add_action('init', array($this, 'addBlockFieldGroup'));

        add_filter('acf/load_field_group', array($this, 'addLocationRulesToBlockGroup'));
        add_filter('allowed_block_types_all', array($this, 'filterBlockTypes'));

        add_filter('render_block', array($this, 'renderCustomGrid'), 10, 2);
        add_filter('render_block', array($this, 'renderAnchor'), 10, 2);
        add_filter('render_block', array($this, 'renderLanguageAttribute'), 1, 2);

        add_filter('render_block_data', array($this, 'blockDataPreRender'), 10, 2);

        add_filter('acf/register_block_type_args', array($this, 'blockTypeArgs'), 10, 1);

        add_filter('Modularity/Block/Settings', array($this, 'customBlockSettings'), 10, 3);
        add_filter('Modularity/Block/Data', array($this, 'customBlockData'), 10, 3);

        add_action('save_post', array($this, 'registerSaveBlockAction'), 10, 3);
    }

    /**
     * > When a post is saved, parse the post content and run the `Modularity/save_block` action for
     * each block.
     *
     * @param int post_id The post id of the post being saved
     * @param object post The post object
     * @param bool update Whether this is an existing post being updated or not.
     */
    public function registerSaveBlockAction(int $post_id, object $post)
    {
        $blocks = parse_blocks($post->post_content);

        if (is_iterable($blocks)) {
            foreach ($blocks as $block) {
                do_action('Modularity/save_block', $block, $post_id, $post);
            }
        }
    }


    /**
     * Add missing width to columns
     * @return array
     */
    public function blockDataPreRender($blockContent, $block)
    {
        if ($block['blockName'] === 'core/columns') {
            foreach ($block['innerBlocks'] as &$innerBlock) {
                if (!isset($innerBlock['attrs']['width'])) {
                    $innerBlock['attrs']['width'] = false;
                }
                if (!$innerBlock['attrs']['width']) {
                    //Calculate the missing width and format number to two decimal points
                    $width = 100 / count($block['innerBlocks']);
                    $width = (string) round($width, 0) . '%';
                    $innerBlock['attrs']['width'] = $width;
                }
            }
        }

        return $block;
    }

    /**
     * Render a custom grid around each column
     * @return string
     */
    public function renderCustomGrid($blockContent, array $block): string
    {
        if (!is_string($blockContent)) {
            return "";
        }

        $widths = [
            '100%' => 'o-grid-12@md',
            '75%'  => 'o-grid-9@md',
            '66%'  => 'o-grid-8@md',
            '50%'  => 'o-grid-6@md',
            '33%'  => 'o-grid-4@md',
            '25%'  => 'o-grid-3@md'
        ];

        if ('core/column' === $block['blockName']) {
            $blockWidth = $widths[$block['attrs']['width'] ?? ''] ?? '';
            $blockContent = '<div class="' . $blockWidth . '">' . $blockContent . '</div>';
        }

        return $blockContent;
    }

    /**
     * If the block has a language attribute, and that language is not the same as the site or page
     * language, then add the language attribute to the block. If no matching id attribute is present on the block then wrap the block in a div with the language attribute.
     *
     * @param blockContent The content of the block.
     * @param array block The block object.
     *
     * @return string The block content with the language attribute added.
     */
    public function renderLanguageAttribute($blockContent, array $block): string
    {
        $siteLanguage = strtolower(get_bloginfo('language'));
        $pageLanguage = strtolower(get_post_meta(get_the_ID(), 'lang', true)) ?: $siteLanguage;
        $blockLanguage = !empty($block['attrs']['data']['lang']) ? strtolower($block['attrs']['data']['lang']) : $pageLanguage;

        if (!in_array($blockLanguage, [$siteLanguage, $pageLanguage]) && $blockLanguage != 'auto') {
            $blockContent = '<div lang="' . htmlspecialchars($blockLanguage, ENT_QUOTES, 'UTF-8') . '">' . $blockContent . '</div>';
        }

        return $blockContent;
    }
    /**
     * Updates the first HTML tag in block content to include a specified anchor ID.
     *
     * This method searches for the first HTML tag in the provided block content and either
     * adds or replaces the 'id' attribute with the value provided in the block's 'anchor' attribute.
     * If the first HTML tag already has an 'id' attribute, its value is replaced with the anchor ID.
     * If the 'id' attribute is not present, it is added with the anchor ID as its value.
     *
     * @param string $blockContent The HTML content of the block.
     * @param array $block The block array containing attributes, including the 'anchor' attribute.
     * @return string The modified block content with the updated 'id' attribute in the first HTML tag.
     */
    public function renderAnchor($blockContent, array $block): string
    {
        if (!empty($block['attrs']['anchor'])) {
            $pattern = '/(<[a-zA-Z0-9]+\s*)(id="[^"]*"|)(.*?>)/';
            $replacement = function ($matches) use ($block) {
                $replacement = $matches[1];
                $replacement .= 'id="' . htmlspecialchars($block['attrs']['anchor'], ENT_QUOTES, 'UTF-8') . '"';
                if (!empty($matches[3])) {
                    $replacement .= $matches[3];
                }
                return $replacement;
            };

            // Perform the replacement
            $blockContent = preg_replace_callback($pattern, $replacement, $blockContent, 1);
        }

        return $blockContent;
    }

    /**
     * Filter out redundant block types except these.
     * @return array
     */
    public function filterBlockTypes($allowedBlocks)
    {
        $registeredBlocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();

        foreach ($registeredBlocks as $type => $block) {
            $allowedCoreBlocks = array(
                'core/columns',
                'core/freeform',
                'core/heading',
                'core/paragraph',
                'core/more',
                'core/list',
                'core/list-item',
                'core/shortcode',
                'core/block',
                'core/image'
            );

            if (str_contains($type, 'core/') && !in_array($type, $allowedCoreBlocks)) {
                unset($registeredBlocks[$type]);
            }
        }

        return array_keys($registeredBlocks);
    }

    /**
     * Add a module category
     * @return array
     */
    public function filterCategories($categories, $post)
    {
        return array_merge(
            $categories,
            array([
                'slug' => 'modules',
                'title' => __('Modules', 'modularity')
            ])
        );
    }

    /**
     * Register all registered and compatible modules as blocks
     * @return void
     */
    public function registerBlocks()
    {
        $enabledModules = \Modularity\ModuleManager::$enabled;

        if (function_exists('acf_register_block_type')) {
            foreach ($this->classes as $class) {
                if ($class->isBlockCompatible && in_array($class->moduleSlug, $enabledModules)) {
                    $blockSettings = [
                        'name'              => str_replace('mod-', '', $class->moduleSlug),
                        'title'             => $class->nameSingular,
                        'icon'              => \Modularity\ModuleManager::getIcon($class),
                        'description'       => $class->description,
                        'render_callback'   => array($this, 'renderBlock'),
                        'category'          => 'modules',
                        'moduleName'        => $class->slug,
                        'mode'              => 'edit',
                        'supports'          => array_merge(
                            [
                                'jsx' => true,
                                'align' => false,
                                'align_text' => false,
                                'align_content' => false
                            ],
                            $class->blockSupports
                        )
                    ];

                    $blockSettings = apply_filters(
                        'Modularity/Block/Settings',
                        $blockSettings,
                        $class->slug
                    );

                    if (!acf_register_block_type($blockSettings)) {
                        error_log("Could not create block for with the id of " . $class->moduleSlug);
                    }
                }
            }
        }
    }


    public function customBlockSettings($blockSettings, $slug)
    {
        if ('script' === $slug) {
            $blockSettings['mode'] = 'edit';
        }

        return $blockSettings;
    }

    /**
     * > This function will add a new variable to the viewData array called `embedContent` and set it
     * to the value of the `embed_code` field in the block's data array
     *
     * @param array viewData The data that will be passed to the view.
     * @param array block The block data
     * @param Modularity\Module\Posts\Posts Object module The module object
     *
     * @return The viewData array is being returned.
     */
    public function customBlockData(array $viewData, array $block, object $module)
    {
        if ('script' === $module->slug) {
            $viewData['embedContent'] = $block['data']['embed_code'];
        }
        return $viewData;
    }
    /**
     * Detect if this may be a module
     *
     * @param string $value
     * @return boolean
     */
    private function isModule(string $value): bool
    {
        foreach ($this->classes as $object) {
            if ($object->moduleSlug === $value) {
                return $object->moduleSlug;
            }
        }
        return false;
    }

    /**
     * Add location rule to each field group to make them avaible to corresponding block
     * @return array
     */
    public function addLocationRule($group)
    {
        $newGroup = $group;

        foreach ($group['location'] as $location) {
            foreach ($location as $locationRule) {
                if ($locationRule['value'] === 'mod-table') {
                    continue;
                }

                // If the location rule that we are trying to add already exists, return original group
                if (str_contains($locationRule['value'], 'acf/') && $locationRule['param'] === 'block') {
                    return $group;
                }

                if ($this->isModule($locationRule['value']) && $locationRule['operator'] === '==') {
                    $newGroup['location'][] = [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => \str_replace(
                                'mod-',
                                'acf/',
                                $locationRule['value']
                            )
                        ]
                    ];
                }
            }
        }
        return $newGroup;
    }

    public function addLocationRulesToBlockGroup($group)
    {
        if ($group['key'] === 'group_block_specific') {
            foreach ($this->classes as $moduleName => $moduleObject) {
                if ($moduleObject->expectsTitleField) {
                    $group['location'][] = [
                        [
                            'param' => 'block',
                            'operator' => '==',
                            'value' => 'acf/' . $moduleName
                        ]
                    ];
                }
            }
        }
        return $group;
    }


    /**
     * Set the default value of fields if value is missing
     * @return array
     */
    private function setDefaultValues($data, $defaultValues)
    {
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => &$dataPoint) {
                if (empty($dataPoint)) {
                    $isSnakeCased = \str_contains($key, '_');

                    if ($isSnakeCased) {
                        $dataPoint = $defaultValues['_' . $key] ?? null;
                    } else {
                        $key = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $key));
                        $dataPoint = $defaultValues['_' . $key] ?? null;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Get the default values of fields
     * @return array
     */
    private function getDefaultValues($blockData)
    {
        $fieldDefaultValues = [];

        foreach ($blockData as $key => $dataPoint) {
            if (stripos($key, '_') === 0) {
                continue;
            }

            if ($defaultValue = get_field_object($key)) {
                if (isset($defaultValue['default_value'])) {
                    $fieldDefaultValues[$key] = $defaultValue['default_value'];
                }
            }
        }

        return $fieldDefaultValues;
    }

    /**
     * The callback used by registerBlocks to render either a block or a notice if validation failed
     * @return void
     */
    public function renderBlock($block)
    {
        global $post;

        $module = $this->classes[$block['moduleName']];

        $cache = new \Modularity\Helper\Cache(
            $post->ID ?? null, [
                $block,
                $module->ID
            ],
            $module->cacheTtl ?? 0
        );

        if ($cache->start()) { //Start cache

            //Append module data, set default values
            $module->data = $this->setDefaultValues(
                $module->data(),
                $this->getDefaultValues($block['data'])
            );

            //Add post title & hide title
            $module->data['postTitle'] = apply_filters(
                'the_title',
                $block['data']['custom_block_title'] ?? $block['data']['field_block_title'] ?? ''
            );
            $module->data['hideTitle'] = $module->data['postTitle'] ? false : true;

            //Set anchor
            if(!empty($block['anchor'])) {
                $block['data']['anchor'] = $block['anchor'];
            }

            //Get view name
            $view = str_replace('.blade.php', '', $module->template());
            $view = !empty($view) ? $view : $block['moduleName'];

            //Add post type
            $viewData = array_merge([
                'post_type' => $module->moduleSlug,
            ], $module->data);

            //Adds block data raw to view
            $viewData['blockData'] = $block;
            // Add block data if missing from current viewData
            foreach ($block['data'] as $key => $data) {
                if (empty($viewData[$key])) {
                    $viewData[$key] = $data;
                }
            }

            //Filter view data
            $viewData = apply_filters('Modularity/Block/Data', $viewData, $block, $module);
            $viewData = apply_filters('Modularity/Block/'.  $block['name'] . '/Data', $viewData, $block, $module);

            if ($this->validateFields($viewData)) {
                $display = new Display(false);
                $renderedView = $display->renderView(
                    $view,
                    $viewData
                );

                //If result is empty, display error for admins
                $viewContainsData = (bool) !empty(preg_replace('/\s+/', '', strip_tags($renderedView, ['img'])));
                if (is_admin() && $module->useEmptyBlockNotice && !$viewContainsData) {
                    $renderedView =  $this->displayNotice(
                        $module->nameSingular,
                        __("Your settings rendered an empty result. Try other settings.", 'modularity')
                    );
                }
            } elseif (is_user_logged_in()) {
                $renderedView = $this->displayNotice(
                    $module->nameSingular,
                    __("Please fill in all required fields.", 'municipio')
                );
            }

            if(!$module->dataFetched) {
                error_log('Class ' . get_class($module) . ' must use the getFields function to ensure block compability.');
            }

            $wrapModule = apply_filters('Modularity/Block/DisplayBlockWrapper', true);
            if ($wrapModule) {
                $classes = [
                    "modularity-mod-{$block['moduleName']}",
                    "block-modularity-mod-{$block['moduleName']}",
                ];

                // Add WordPress' extra classes
                if (isset($block['className']) && !empty($block['className'])) {
                    $classes = array_merge($classes, explode(' ', $block['className']));
                }

                $renderedView = '<div class="' . implode(' ', $classes) . '">' . $renderedView . '</div>';
            }

            // Render block view if validated correctly
            echo $renderedView;

            $cache->stop(); //Stop cache
        }
    }

    /**
     * Validates the required fields
     * @return boolean
     */
    private function validateFields($fields, $whitelist = ['hideTitle', 'postTitle'])
    {
        $valid = true;

        foreach ($fields as $key => $value) {
            //Whitelisted (do not check)
            if (in_array($key, $whitelist)) {
                continue;
            }

            $fieldObject = get_field_object($key);

            if (is_array($fieldObject) && !empty($fieldObject)) {
                //Skip validation of decendants
                if (isset($fieldObject['parent']) && str_contains($fieldObject['parent'], 'field_')) {
                    continue;
                }

                //Check if required field has a value
                if ($fieldObject['required'] && (!$fieldObject['value'] && $fieldObject['value'] !== "0")) {
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    /**
     * Add block containing custom block title field.
     * Replaces post title field.
     *
     * @return void
     */
    public function addBlockFieldGroup()
    {
        acf_add_local_field_group(array(
            'menu_order' => -1,
            'key' => 'group_block_specific',
            'title' => __("Block settings", 'modularity'),
            'location' => array(),
            'fields' => array(
                array(
                    'key' => 'field_block_title',
                    'label' => __("Title", 'modularity'),
                    'name' => 'custom_block_title',
                    'type' => 'text',
                )
            )
        ));
    }

    /**
     * Returns (error) notices to user.
     * Rendered by notice component via notice module.
     *
     * @return string
     */
    private function displayNotice($moduleName, $message)
    {
        $display = new Display();
        $view = 'notice';
        $noticeData = array(
            'hideTitle' => false,
            'post_type' => 'mod-notice',
            'postTitle' => $moduleName,
            'notice_text' => $message,
            'notice_type' => 'info',
            'icon' => array('name' => 'info'),
            'postTitle' => $moduleName,
        );

        return $display->renderView($view, $noticeData);
    }

    /**
     * Default block type arguments
     *
     * @param array $args
     * @return array
     */
    public function blockTypeArgs($args)
    {
        $args['supports'] = array_merge(
            $args['supports'],
            [
                'anchor' => true
            ]
        );
        return $args;
    }
}
