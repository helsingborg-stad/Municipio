<?php

namespace Municipio;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;

/**
 * Class App
 *
 * @package Municipio
 */
class Upgrade
{
    private $dbVersion    = 32; //The db version we want to achive
    private $dbVersionKey = 'municipio_db_version';
    private $db;

    /**
     * App constructor.
     */
    public function __construct(
        private GetThemeMod&GetPostTypes $wpService,
        private UpdateField&GetField $acfService
    ) {
        //Development tools
        //WARNING: Do not use in PROD. This will destroy your db.
        /*add_action('init', array($this, 'reset'), 1);
        add_action('init', array($this, 'debugPre'), 5);
        add_action('init', array($this, 'debugAfter'), 20);*/

        //Production hook
        add_action('wp', array($this, 'initUpgrade'), 1);
    }

    /**
     * Enable to print stuff you need.
     *
     * @return void
     */
    public function debugAfter()
    {
        echo '<h2>After upgrade</h2>';
        echo '<pre style="overflow: auto; max-height: 50vh; margin: 20px; padding: 10px; border: 2px solid #f00;">';
        print_r(get_theme_mods());
        echo '</pre>';
    }

    /**
     * Enable to print stuff you need.
     *
     * @return void
     */
    public function debugPre()
    {
        echo '<h2>Before upgrade</h2>';
        echo '<pre style="overflow: auto; max-height: 50vh; margin: 20px; padding: 10px; border: 2px solid #f00;">';
        print_r(get_theme_mods());
        echo '</pre>';
    }

    /**
     * Reset db version, in order to run all scripts from the beginning.
     *
     * @return void
     */
    public function reset()
    {
        update_option($this->dbVersionKey, 1);
    }

    /**
     * Upgrade database,
     * when you want to upgrade database,
     * create a new function and increase
     * $this->dbVersion.
     *
     * Method inspiration from WordPress Core.
     *
     * @return boolean
     */
    private function v_1($db): bool
    {
        //Copy and update code here
        return true; //Return false to keep running this each time!
    }

    // Migrate width from acf to kirki
    private function v_5($db): bool
    {

    //Move
        $this->migrateThemeMod('widths', 'container', 'field_609bdcc8348d6');
        $this->migrateThemeMod('widths', 'container_frontpage', 'field_60928f237c070');
        $this->migrateThemeMod('widths', 'container_archive', 'field_609bdcad348d5');
        $this->migrateThemeMod('widths', 'container_content', 'field_609298276e5b2');

        $this->migrateThemeMod('widths', 'column_size_left', 'field_60d339b60049e');
        $this->migrateThemeMod('widths', 'column_size_right', 'field_60d3393d1231a');

        $this->deleteThemeMod('widths');

        return true;
    }

    //Migrate navigation position.
    private function v_6($db): bool
    {
        $this->migrateThemeMod('general', 'secondary_navigation_position', 'field_60cb4dd897cb8');
        $this->deleteThemeMod('general');
        $this->deleteThemeMod('mobilemenu');

        return true;
    }

    //Migrate radius.
    private function v_7($db): bool
    {
        $this->migrateThemeMod('radius', 'radius_xs', 'field_603662f7a16f8');
        $this->migrateThemeMod('radius', 'radius_sm', 'field_6038fa31cfac6');
        $this->migrateThemeMod('radius', 'radius_md', 'field_6038fa400384b');
        $this->migrateThemeMod('radius', 'radius_lg', 'field_6038fa52576ba');

        $this->deleteThemeMod('radius');

        return true;
    }

    //Migrate header stuff.
    private function v_8($db): bool
    {
        if (get_theme_mod('header')) {
            $this->migrateThemeMod('header', 'header_sticky', 'field_61434d3478ef7');
            $this->migrateThemeMod('header', 'header_background', 'field_61446365d1c7e');
            $this->migrateThemeMod('header', 'header_color', 'field_614467575de00');
            $this->migrateThemeMod('header', 'header_modifier', 'field_6070186956c15');
        }

        $this->deleteThemeMod('header');

        return true;
    }

    //Migrate header.
    private function v_9($db): bool
    {
        $this->migrateThemeMod('padding', 'main_content_padding', 'field_611e43ec4dfa5');

        $this->deleteThemeMod('padding');

        return true;
    }

    //Migrate quicklinks stuff.
    private function v_10($db): bool
    {
        $this->migrateThemeMod('quicklinks', 'quicklinks_background_type', 'field_61570dd479d9b');
        $this->migrateThemeMod('quicklinks', 'quicklinks_custom_background', 'field_61570e6979d9c');
        $this->migrateThemeMod('quicklinks', 'quicklinks_background', 'field_6123844e0f0bb');
        $this->migrateThemeMod('quicklinks', 'quicklinks_color', 'field_6127571bcc76e');
        $this->migrateThemeMod('quicklinks', 'quicklinks_sticky', 'field_61488b616937c');
        $this->migrateThemeMod('quicklinks', 'quicklinks_location', 'field_61488c4f6b4fd');

        $this->deleteThemeMod('quicklinks');

        return true;
    }

    //Migrate hero stuff.
    private function v_11($db): bool
    {
        $overlays = get_theme_mod('hero');

        $defaultColor   = $overlays['field_614c713ae73ea']['field_614c7189e73eb'];
        $defaultOpacity = $overlays['field_614c713ae73ea']['field_614c7197e73ec'];

        $vibrantColor   = $overlays['field_614c720fb65a4']['field_614c720fb65a5'];
        $vibrantOpacity = $overlays['field_614c720fb65a4']['field_614c720fb65a6'];

        $defaultOverlay = $this->hex2rgba($defaultColor, "0." . (int)$defaultOpacity);
        $vibrantOverlay = $this->hex2rgba($vibrantColor, "0." . (int)$vibrantOpacity);

        if ($vibrantColor || $defaultColor) {
            if ($vibrantColor == 'rgb(0,0,0)' && $defaultColor == 'rgb(0,0,0)') {
                set_theme_mod('hero_overlay_enable', 0);
            } else {
                set_theme_mod('hero_overlay_enable', 1);
            }
        } else {
            set_theme_mod('hero_overlay_enable', 0);
        }



        $this->deleteThemeMod('hero');

        return true;
    }

    //Migrate overlay stuff.
    private function v_12($db): bool
    {
        $overlays = get_theme_mod('overlay');
        if ($overlays) {
            $color   = $overlays['field_615c1bc3772c6']['field_615c1bc3780b0'];
            $opacity = $overlays['field_615c1bc3772c6']['field_615c1bc3780b6'];
            $overlay = $this->hex2rgba($color, "0." . (int)$opacity);
            set_theme_mod('overlay', $overlay);
        }

        return true;
    }

    //Migrate modules stuff.
    private function v_13($db): bool
    {
        if (get_theme_mod('site')) {
            $this->migrateThemeMod('site', 'header_modifier', 'field_6070186956c15');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'header_modifier', 'field_6070186956c15');
        }

        if (get_theme_mod('posts')) {
            $this->migrateThemeMod('posts', 'mod_posts_index_modifier', 'field_6061d864c6873');
            $this->migrateThemeMod('posts', 'mod_posts_list_modifier', 'field_6062fd67a2eb4');
            $this->migrateThemeMod('posts', 'mod_posts_expandablelist_modifier', 'field_60631bb52591c');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'mod_posts_index_modifier', 'field_6061d864c6873');
            $this->migrateThemeMod('modules', 'mod_posts_list_modifier', 'field_6062fd67a2eb4');
            $this->migrateThemeMod('modules', 'mod_posts_expandablelist_modifier', 'field_60631bb52591c');
        }

        if (get_theme_mod('contacts')) {
            $this->migrateThemeMod('contacts', 'mod_contacts_list_modifier', 'field_6063008d5068a');
            $this->migrateThemeMod('contacts', 'mod_contacts_card_modifier', 'field_6090f318a40ef');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'mod_contacts_list_modifier', 'field_6063008d5068a');
            $this->migrateThemeMod('modules', 'mod_contacts_card_modifier', 'field_6090f318a40ef');
        }

        if (get_theme_mod('inlay')) {
            $this->migrateThemeMod('inlay', 'mod_inlay_list_modifier', 'field_606300da5068b');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'mod_inlay_list_modifier', 'field_606300da5068b');
        }

        $this->migrateThemeMod('modules', 'mod_map_modifier', 'field_6063013a5068c');

        if (get_theme_mod('script')) {
            $this->migrateThemeMod('script', 'mod_script_modifier', 'field_6063072c25917');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'mod_script_modifier', 'field_6063072c25917');
        }

        if (get_theme_mod('text')) {
            $this->migrateThemeMod('text', 'mod_text_modifier', 'field_60631b4025918');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'mod_text_modifier', 'field_60631b4025918');
        }

        if (get_theme_mod('video')) {
            $this->migrateThemeMod('video', 'mod_video_modifier', 'field_60631b5f25919');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'mod_video_modifier', 'field_60631b5f25919');
        }

        if (get_theme_mod('index')) {
            $this->migrateThemeMod('index', 'mod_index_modifier', 'field_607843a6ba55e');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'mod_index_modifier', 'field_607843a6ba55e');
        }

        if (get_theme_mod('localevent')) {
            $this->migrateThemeMod('localevent', 'mod_localevent_modifier', 'field_607ff0d6b8426');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'mod_localevent_modifier', 'field_607ff0d6b8426');
        }

        if (get_theme_mod('sectionssplit')) {
            $this->migrateThemeMod('sectionssplit', 'mod_section_split_modifier', 'field_611f83757a727');
        } elseif (get_theme_mod('modules')) {
            $this->migrateThemeMod('modules', 'mod_section_split_modifier', 'field_611f83757a727');
        }

        $this->deleteThemeMod('modules');
        $this->deleteThemeMod('site');
        $this->deleteThemeMod('posts');
        $this->deleteThemeMod('contacts');
        $this->deleteThemeMod('index');
        $this->deleteThemeMod('inlay');
        $this->deleteThemeMod('script');
        $this->deleteThemeMod('localevent');
        $this->deleteThemeMod('sectionssplit');
        $this->deleteThemeMod('text');
        $this->deleteThemeMod('video');
        $this->deleteThemeMod('card');

        return true;
    }

    //Migrate colors stuff.
    private function v_14($db): bool
    {
        $this->migrateThemeMod('colors', 'color_palette_primary.base', 'field_60361bcb76325');
        $this->migrateThemeMod('colors', 'color_palette_primary.dark', 'field_60364d06dc120');
        $this->migrateThemeMod('colors', 'color_palette_primary.light', 'field_603fba043ab30');

        $this->migrateThemeMod('colors', 'color_palette_secondary.base', 'field_603fba3ffa851');
        $this->migrateThemeMod('colors', 'color_palette_secondary.dark', 'field_603fbb7ad4ccf');
        $this->migrateThemeMod('colors', 'color_palette_secondary.light', 'field_603fbbef1e2f8');

        $this->migrateThemeMod('colors', 'color_link.link', 'field_60868021879b6');
        $this->migrateThemeMod('colors', 'color_link.link_hover', 'field_608680ef879b7');
        $this->migrateThemeMod('colors', 'color_link.visited', 'field_60868147879b8');
        $this->migrateThemeMod('colors', 'color_link.visited_hover', 'field_6086819f879b9');
        $this->migrateThemeMod('colors', 'color_link.active', 'field_608681df879ba');

        $this->migrateThemeMod('colors', 'color_background.complementary', 'field_60911ccc38857');

        $this->deleteThemeMod('colors');

        return true;
    }

    //Migrate header apperance
    private function v_15($db): bool
    {
        if (get_option('options_header_layout')) {
            set_theme_mod('header_apperance', get_option('options_header_layout'));
        }

        delete_option('options_header_layout');
        return true;
    }

    //Consolidate overlay
    private function v_16($db): bool
    {
        $overlay = get_theme_mod(
            'hero_overlay_vibrant',
            get_theme_mod(
                'hero_overlay_neutral',
                get_theme_mod('overlay', "rgba(0,0,0,0.55)")
            )
        );

        if ($overlay) {
            set_theme_mod('color_alpha', array('base' => $overlay));
        }

        $this->deleteThemeMod('hero_overlay_enable');
        $this->deleteThemeMod('hero_overlay_vibrant');
        $this->deleteThemeMod('hero_overlay_neutral');
        $this->deleteThemeMod('overlay');

        return true;
    }

    //Set contrasting colors (if no default)
    private function v_17($db): bool
    {
        if (
            !empty(get_theme_mod('color_palette_primary')) &&
            empty(get_theme_mod('color_palette_primary')['contrasting'])
        ) {
            $this->setAssociativeThemeMod('color_palette_primary.contrasting', '#ffffff');
        }

        if (
            !empty(get_theme_mod('color_palette_secondary')) &&
            empty(get_theme_mod('color_palette_secondary')['contrasting'])
        ) {
            $this->setAssociativeThemeMod('color_palette_secondary.contrasting', '#ffffff');
        }

        return true;
    }

    //Move enable gutenberg to a more dynamic setting
    private function v_18($db): bool
    {
        $previousSetting = get_option('activate_gutenberg_editor');

        if ($previousSetting) {
            update_option('gutenberg_editor_mode', 'all');
        } else {
            update_option('gutenberg_editor_mode', 'disabled');
        }

        delete_option('activate_gutenberg_editor');

        return true;
    }

    //Move enable gutenberg to a more dynamic setting
    private function v_19($db): bool
    {
        $previousSetting = get_option('options_activate_gutenberg_editor');

        if ($previousSetting) {
            update_option('gutenberg_editor_mode', 'all');
        } else {
            update_option('gutenberg_editor_mode', 'disabled');
        }

        delete_option('options_activate_gutenberg_editor');
        delete_option('_options_activate_gutenberg_editor');

        return true;
    }

    //Move archive apperance settings to customizer
    private function v_20($db): bool
    {
        $postTypes = $this->getAllPostTypes();

        //Translation sheme
        $scheme = [
            'title'                 => 'heading',
            'post_style'            => 'style',
            'number_of_posts'       => 'post_count',
            'sort_key'              => 'order_by',
            'sort_order'            => 'order_direction',
            'post_taxonomy_display' => 'taxonomies_to_display'
        ];

        if (is_array($postTypes) && !empty($postTypes)) {
            foreach ($postTypes as $postType) {
                $fromId = isset($postType->name) ?  'options_archive_' . $postType->name . '_' : false;
                $toId   = isset($postType->name) ?  'archive_' . $postType->name . '_' : false;

                if ($fromId != false) {
                    //Plain transfer according to scheme
                    foreach ($scheme as $oldKey => $newKey) {
                        set_theme_mod(
                            $toId . $newKey,
                            get_option($fromId . $oldKey) ?? null
                        );
                        delete_option($fromId . $oldKey); //Clean old option
                    }

                    //Move active filters
                    $filters = array_merge(
                        (array) get_option($fromId . 'feed_filtering_settings') ?? [],
                        (array) get_option($fromId . 'post_filters_header') ?? [],
                        (array) get_option($fromId . 'post_filters_sidebar') ?? []
                    );
                    set_theme_mod($toId . 'enabled_filters', array_filter($filters));
                    delete_option($fromId . 'feed_filtering_settings'); //Clean old option
                    delete_option($fromId . 'post_filters_header'); //Clean old option
                    delete_option($fromId . 'post_filters_sidebar'); //Clean old option

                    //Transfer columns
                    $columns = (int) preg_replace('/[^0-9]/', '', get_option($fromId . 'grid_columns')) ?? '4';
                    if (empty($columns)) {
                        $columns = '4';
                    }
                    set_theme_mod($toId . 'number_of_columns', (int) floor(12 / $columns));
                    delete_option($fromId . 'grid_columns'); //Clean old option
                }
            }
        }

        return true;
    }

    //Move footer logo settings to customizer
    private function v_21($db): bool
    {
        if ($logotype = get_option('options_footer_logotype')) {
            set_theme_mod('footer_logotype', $logotype);
        }

        delete_option('options_footer_logotype');
        return true;
    }



    /**
     * Move logos from theme options to Customizer
     */
    private function v_22($db): bool
    {
        $this->migrateACFOptionImageIdToThemeModUrl('logotype', 'logotype');
        $this->migrateACFOptionImageIdToThemeModUrl('logotype_negative', 'logotype_negative');
        $this->migrateACFOptionImageIdToThemeModUrl('logotype_emblem', 'logotype_emblem');
        $this->migrateACFOptionToThemeMod('header_logotype', 'header_logotype');

        return true;
    }

    /**
     * Move search settings to customizer.
     */
    private function v_23($db): bool
    {
        $this->migrateACFOptionToThemeMod('search_display', 'search_display');
        return true;
    }

    /**
     * Publish migrated logotypes (v_22) to the design API.
     */
    private function v_24($db): bool
    {
        do_action('municipio_store_theme_mod');
        return true;
    }

    /**
     * Publish migrated logotypes (v_22) to the design API.
     */
    private function v_25($db): bool
    {
        $this->migrateThemeMod('hamburger_menu_appearance_type', 'mega_menu_appearance_type');
        $this->migrateThemeMod('hamburger_menu_custom_colors', 'mega_menu_custom_colors');
        $this->migrateThemeMod('hamburger_menu_font', 'mega_menu_font');
        $this->migrateThemeMod('hamburger_menu_item_style', 'mega_menu_item_style');
        $this->migrateThemeMod('hamburger_menu_item_button_style', 'mega_menu_item_button_style');
        $this->migrateThemeMod('hamburger_menu_item_button_color', 'mega_menu_item_button_color');
        $this->migrateThemeMod('hamburger_menu_color_scheme', 'mega_menu_color_scheme');
        $this->migrateThemeMod('hamburger_menu_mobile', 'mega_menu_mobile');

        $menuLocations = get_theme_mod('nav_menu_locations');
        if (!empty($menuLocations) && isset($menuLocations['hamburger-menu'])) {
            $menuLocations['mega-menu'] = $menuLocations['hamburger-menu'];
            unset($menuLocations['hamburger-menu']);
            set_theme_mod('nav_menu_locations', $menuLocations);
        }

        return true;
    }

    private function v_26($db): bool
    {
        $drawerSizes = get_theme_mod('drawer_screen_sizes');
        if (!empty($drawerSizes) && is_array($drawerSizes) && in_array('lg', $drawerSizes)) {
            array_push($drawerSizes, 'xl');
            set_theme_mod('drawer_screen_sizes', $drawerSizes);
        }
        return true;
    }

    private function v_27($db): bool
    {
        $searchLocations = get_theme_mod('search_display');
        if (!empty($searchLocations) && is_array($searchLocations) && in_array('hamburger_menu', $searchLocations) && !in_array('mega_menu', $searchLocations)) {
            array_push($searchLocations, 'mega_menu');
            set_theme_mod('search_display', $searchLocations);
        }

        return true;
    }

    private function v_28($db): bool
    {
        $args = array(
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => 'inherit',
            'post_mime_type' => 'application/font-woff'
        );

        $woffFilesQuery = new \WP_Query($args);

        if (!empty($woffFilesQuery->posts)) {
            $uploadsInstance = new \Municipio\Admin\Uploads();
            foreach ($woffFilesQuery->posts as $woffFile) {
                if (!get_post_meta($woffFile->ID, 'ttf')) {
                    $uploadsInstance->convertWOFFToTTF($woffFile->ID);
                }
            }
        }
        return true;
    }

    private function v_29($db): bool
    {
        $args = [
            'posts_per_page' => -1,
            'meta_key'       => 'location',
            'post_type'      => 'any',
            'post_status'    => 'publish'
        ];

        $posts = get_posts($args);
        if (!empty($posts) && is_array($posts)) {
            foreach ($posts as $post) {
                $schemaField = get_field('schema', $post->ID) ?? [];
                if (is_array($schemaField)) {
                    $locationField      = get_post_meta($post->ID, 'location', true);
                    $schemaField['geo'] = !empty($schemaField['geo']) ? $schemaField['geo'] : $locationField;

                    update_field('schema', $schemaField, $post->ID);
                }
            }
        }

        return true;
    }

    //Retires custom favicon in favour of native functionality
    private function v_30($db): bool
    {
        $nativeFaviconKey = "site_icon";
        $nativeFavicon    = get_option($nativeFaviconKey, false);
        if (!$nativeFavicon) {
            foreach (['152', '144', 'fav'] as $type) {
                for ($i = 0; $i < 10; $i++) {
                    $iconType = get_option('options_favicons_' . $i . '_favicon_type');
                    if ($iconType == $type) {
                        $iconId = get_option('options_favicons_' . $i . '_favicon_icon');
                        if (is_numeric($iconId)) {
                            update_option($nativeFaviconKey, $iconId);
                        }
                        break 2;
                    }
                }
            }
        }

        return true;
    }

    private function v_31($db): bool
    {
        $db->query("DELETE FROM {$db->postmeta} WHERE meta_key LIKE '_oembed%'");
        return true;
    }

    private function v_32($db): bool
    {
        update_option('css', []);
        return true;
    }

    /**
     * Migrate schema type settings from post type options to the new common interface.
     *
     * @param \wpdb $db
     */
    public function v_33($db): bool // phpcs:ignore
    {
        $destinationValues = [];
        foreach ($this->wpService->getPostTypes() as $postType) {
            $schemaType = $this->acfService->getField('schema', $postType . '_options');

            if (empty($schemaType)) {
                continue;
            }

            $destinationValues[] = [
                'post_type'   => $postType,
                'schema_type' => $schemaType
            ];
        }

        if (!empty($destinationValues)) {
            $this->acfService->updateField('post_type_schema_types', $destinationValues, 'option');
        }

        return true;
    }

    /**
     * Get all post types
     *
     * @return array
     */
    private function getAllPostTypes()
    {
        $postTypes = array();
        foreach (get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public || $args->name === 'page') {
                continue;
            }

            $postTypes[$postType] = $args;
        }

        $postTypes['author'] = (object) array(
            'name'              => 'author',
            'label'             => __('Author'),
            'has_archive'       => true,
            'is_author_archive' => true
        );

        return $postTypes;
    }

    /**
     * Move and clean out the old theme mod
     *
     * @param string $oldKey
     * @param string $newKey
     * @return bool
     */
    private function migrateThemeMod($oldKey, $newKey, $subkey = null)
    {
        if ($oldValue = get_theme_mod($oldKey)) {
            if ($subkey && isset($oldValue[$subkey])) {
                return $this->setAssociativeThemeMod($newKey, $oldValue[$subkey]);
            } elseif (is_null($subkey)) {
                return $this->setAssociativeThemeMod($newKey, $oldValue);
            }
        }
        return false;
    }

    /**
     * Logs error message
     *
     * @param string $message Error message
     *
     */
    private function logError(string $message)
    {
        error_log($message);
    }

    /**
     * Migrates an ACF option to a theme mod.
     *
     * @param string $option The option key which is being migrated.
     * @param string $themeMod [Optional] The theme mod key to which the
     * option is being migrated. If not provided, it will take the value of $option.
     *
     */
    private function migrateACFOptionToThemeMod(string $option, string $themeMod)
    {
        $errorMessage = "Failed to migrate ACF option \"$option\" to theme mod \"$themeMod\"";

        if (
            !function_exists('get_field') ||
            empty($value = get_field($option, 'option', false)) ||
            !set_theme_mod($themeMod, $value)
        ) {
            $this->logError($errorMessage);
            return;
        }

        delete_field($option, 'option');
    }

    /**
     * Migrates an ACF option image id to a theme mod url.
     *
     * @param string $option The option key which is being migrated.
     * @param string $themeMod [Optional] The theme mod key to which the option is
     * being migrated. If not provided, it will take the value of $option.
     *
     */
    private function migrateACFOptionImageIdToThemeModUrl(string $option, string $themeMod)
    {
        $errorMessage = "Failed to migrate ACF option \"$option\" to theme mod \"$themeMod\"";

        if (!function_exists('get_field')) {
            $this->logError($errorMessage);
            return;
        }

        $value = get_field($option, 'option', false);

        if (empty($value = get_field($option, 'option', false)) || !is_int(absint($value))) {
            $this->logError($errorMessage);
            return;
        }

        $attachmentUrl = wp_get_attachment_url($value);

        if ($attachmentUrl === false || !set_theme_mod($themeMod, $attachmentUrl)) {
            $this->logError($errorMessage);
            return;
        }

        delete_field($option, 'option');
    }

    /**
     * A simple wrapper around set_theme_mod() in order to set a single property value of an associative array setting.
     * Key should include a dot in order to target a property.
     * eg. color_palette.primary will target array('primary' => VALUE).
     *
     * Does not support nested values eg settings.property.nested_value_1.nested_value_2 etc
     *
     * @param string $key
     * @param string $value
     * @param bool $castToArray this will transform existing values which are not arrays to empty arrays when true
     * @return bool True if the value was updated, false otherwise.
     */
    protected function setAssociativeThemeMod($key, $value, $castToArray = false)
    {
        $parsedString = explode('.', $key);
        $key          = $parsedString[0] ?? '';
        $property     = $parsedString[1] ?? '';

        if (empty($parsedString) || empty($key)) {
            return false;
        }

        if (!empty($property)) {
            $associativeArr = get_theme_mod($key, []);
            $associativeArr = is_array($associativeArr) || $castToArray !== true ? $associativeArr : [];

            if (!is_array($associativeArr)) {
                $errorMessage = "Failed to migrate setting (" . $key . "." . $property . ").
                The specified setting already exists and is not an associative array.";
                $this->logError($errorMessage);
                return false;
            }

            $associativeArr[$property] = $value;
            $value                     = $associativeArr;
        }

        return set_theme_mod($key, $value);
    }

    /**
     * Deletes a theme mod
     *
     * @param string $key
     * @return bool
     */
    private function deleteThemeMod($key)
    {
        return remove_theme_mod($key);
    }

    /**
     * Undocumented function
     *
     * @param [type] $color
     * @param boolean $opacity
     * @return void
     */
    private function hex2rgba($color, $opacity = false)
    {
        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if (empty($color)) {
            return $default;
        }

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif (strlen($color) == 3) {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1) {
                $opacity = 1.0;
            }
            $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }

        //Return rgb(a) color string
        return $output;
    }

    /**
     * Run upgrade functions
     *
     * @return void
     */
    public function initUpgrade()
    {
        $currentDbVersion = is_numeric(get_option($this->dbVersionKey)) ? (int) get_option($this->dbVersionKey) : 1;

        if ($this->dbVersion != $currentDbVersion) {
            if (!is_numeric($this->dbVersion)) {
                wp_die(__('To be installed database version must be a number.', 'municipio'));
            }

            if (!is_numeric($currentDbVersion)) {
                $this->logError(__('Current database version must be a number.', 'municipio'));
            }

            if ($currentDbVersion > $this->dbVersion) {
                $this->logError(
                    __(
                        'Database cannot be lower than currently installed (cannot downgrade).',
                        'municipio'
                    )
                );
            }

            //Fetch global wpdb object, save to $db
            $this->globalToLocal('wpdb', 'db');

            //Run upgrade(s)
            while ($currentDbVersion <= $this->dbVersion) {
                $currentDbVersion++;
                $funcName = 'v_' . (string) $currentDbVersion;

                $lockKey  = 'upgrade_lock_v' . $currentDbVersion;
                $isLocked = get_transient($lockKey);
                if (!$isLocked && method_exists($this, $funcName)) {
                    set_transient($lockKey, time(), 600);
                    if ($this->{$funcName}($this->db)) {
                        update_option($this->dbVersionKey, (int) $currentDbVersion);
                        wp_cache_flush();
                    }

                    delete_transient($lockKey);
                }
            }
        }
    }

    /**
     * Creates a local copy of the global instance
     * The target var should be defined in class header as private or public
     * @param string $global The name of global varable that should be made local
     * @param string $local Handle the global with the name of this string locally
     * @return void
     */
    private function globalToLocal($global, $local = null)
    {
        global $$global;

        if (is_null($$global)) {
            return false;
        }

        if (is_null($local)) {
            $this->$global = $$global;
        } else {
            $this->$local = $$global;
        }

        return true;
    }
}
