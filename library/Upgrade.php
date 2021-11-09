<?php

namespace Municipio;

/**
 * Class App
 * @package Municipio
 */
class Upgrade
{
    private $dbVersion = 14; //The db version we want to achive
    private $dbVersionKey = 'municipio_db_version';
    private $db;

    /**
     * App constructor.
     */
    public function __construct()
    {
        //Development tools
        //WARNING: Do not use in PROD. This will destroy your db.
        /*add_action('init', array($this, 'reset'), 1);
        add_action('init', array($this, 'debugPre'), 5);
        add_action('init', array($this, 'debugAfter'), 20); */

        //Production hook
        add_action('init', array($this, 'initUpgrade'), 10);
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
        update_option('theme_mods_municipio', unserialize('a:22:{s:18:"custom_css_post_id";i:-1;s:16:"sidebars_widgets";a:2:{s:4:"time";i:1493903594;s:4:"data";a:4:{s:19:"wp_inactive_widgets";a:0:{}s:9:"sidebar-1";a:6:{i:0;s:8:"search-2";i:1;s:14:"recent-posts-2";i:2;s:17:"recent-comments-2";i:3;s:10:"archives-2";i:4;s:12:"categories-2";i:5;s:6:"meta-2";}s:9:"sidebar-2";a:0:{}s:9:"sidebar-3";a:0:{}}}s:18:"nav_menu_locations";a:5:{s:9:"main-menu";i:2;s:19:"dropdown-links-menu";i:14;s:15:"quicklinks-menu";i:15;s:14:"secondary-menu";i:2;s:13:"mobile-drawer";i:15;}s:7:"general";a:1:{s:19:"field_60cb4dd897cb8";s:5:"right";}s:6:"colors";a:23:{s:19:"field_60361bcb76325";s:7:"#7B075E";s:19:"field_60364d06dc120";s:7:"#4b0034";s:19:"field_603fba043ab30";s:7:"#ad428b";s:19:"field_608c016efa73a";a:2:{s:19:"field_608c0259fa73b";s:7:"#ad428b";s:19:"field_608c02dcfa73c";s:2:"10";}s:19:"field_608c0375f70cd";a:2:{s:19:"field_608c0375f70ce";s:7:"#ad428b";s:19:"field_608c0375f70cf";s:2:"25";}s:19:"field_603fba3ffa851";s:7:"#d35098";s:19:"field_603fbb7ad4ccf";s:7:"#9e166a";s:19:"field_603fbbef1e2f8";s:7:"#ff82c9";s:19:"field_608c038cf70d0";a:2:{s:19:"field_608c038cf70d1";s:7:"#ff82c9";s:19:"field_608c038cf70d2";s:2:"10";}s:19:"field_608c03bcf70d3";a:2:{s:19:"field_608c03bcf70d4";s:7:"#ff82c9";s:19:"field_608c03bcf70d5";s:2:"25";}s:19:"field_608c0e753ef05";s:7:"#ec6701";s:19:"field_608c0e813ef06";s:7:"#b23700";s:19:"field_608c0e8c3ef07";s:7:"#ff983e";s:19:"field_608c0ea33ef08";a:2:{s:19:"field_608c0ea33ef09";s:7:"#ae0b05";s:19:"field_608c0ea33ef0a";s:2:"75";}s:19:"field_608c0eae3ef0b";a:2:{s:19:"field_608c0eae3ef0c";s:7:"#ae0b05";s:19:"field_608c0eae3ef0d";s:2:"75";}s:19:"field_60868021879b6";s:7:"#4b0034";s:19:"field_608680ef879b7";s:7:"#ad428b";s:19:"field_60868147879b8";s:7:"#4b0034";s:19:"field_6086819f879b9";s:7:"#ad428b";s:19:"field_608681df879ba";s:7:"#7b075e";s:19:"field_60911ccc38857";s:7:"#d4c2ce";s:19:"field_6091280638858";s:7:"#e8dae4";s:19:"field_6091282d38859";s:7:"#efe4eb";}s:6:"widths";a:6:{s:19:"field_609bdcc8348d6";s:4:"1284";s:19:"field_60928f237c070";s:4:"1284";s:19:"field_609bdcad348d5";s:4:"1284";s:19:"field_609298276e5b2";s:3:"688";s:19:"field_60d339b60049e";s:5:"large";s:19:"field_60d3393d1231a";s:5:"large";}s:7:"padding";a:1:{s:19:"field_611e43ec4dfa5";s:1:"4";}s:6:"header";a:4:{s:19:"field_61434d3478ef7";s:6:"sticky";s:19:"field_61446365d1c7e";s:0:"";s:19:"field_614467575de00";s:0:"";s:19:"field_6070186956c15";s:8:"accented";}s:10:"mobilemenu";a:1:{s:19:"field_61126702da36c";s:7:"duotone";}s:10:"quicklinks";a:5:{s:19:"field_61570dd479d9b";s:3:"hex";s:19:"field_61570e6979d9c";s:7:"#540540";s:19:"field_6127571bcc76e";s:10:"text-white";s:19:"field_61488b616937c";s:0:"";s:19:"field_61488c4f6b4fd";s:10:"everywhere";}s:4:"hero";a:2:{s:19:"field_614c713ae73ea";a:2:{s:19:"field_614c7189e73eb";s:7:"#310326";s:19:"field_614c7197e73ec";s:2:"64";}s:19:"field_614c720fb65a4";a:2:{s:19:"field_614c720fb65a5";s:7:"#310326";s:19:"field_614c720fb65a6";s:2:"72";}}s:4:"card";a:1:{s:19:"field_609128593885a";s:7:"#f6edf3";}s:7:"overlay";a:1:{s:19:"field_615c1bc3772c6";a:2:{s:19:"field_615c1bc3780b0";s:7:"#310326";s:19:"field_615c1bc3780b6";s:2:"72";}}s:13:"sectionssplit";a:1:{s:19:"field_611f83757a727";s:9:"highlight";}s:5:"posts";a:3:{s:19:"field_6061d864c6873";s:8:"accented";s:19:"field_6062fd67a2eb4";s:8:"accented";s:19:"field_60631bb52591c";s:4:"none";}s:8:"contacts";a:2:{s:19:"field_6063008d5068a";s:9:"highlight";s:19:"field_6090f318a40ef";s:9:"highlight";}s:5:"inlay";a:1:{s:19:"field_606300da5068b";s:8:"accented";}s:4:"text";a:1:{s:19:"field_60631b4025918";s:9:"highlight";}s:5:"video";a:1:{s:19:"field_60631b5f25919";s:9:"highlight";}s:6:"script";a:1:{s:19:"field_6063072c25917";s:9:"highlight";}s:5:"index";a:1:{s:19:"field_607843a6ba55e";s:9:"highlight";}s:10:"localevent";a:1:{s:19:"field_607ff0d6b8426";s:8:"accented";}}'));
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
    private function v_1($db) : bool
    {
        //Copy and update code here
    return true; //Return false to keep running this each time!
    }

    // Migrate width from acf to kirki
    private function v_5($db) : bool
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
    private function v_6($db) : bool
    {
        $this->migrateThemeMod('general', 'secondary_navigation_position', 'field_60cb4dd897cb8');
        $this->deleteThemeMod('general');
        $this->deleteThemeMod('mobilemenu');

        return true;
    }

    //Migrate radius.
    private function v_7($db) : bool
    {
        $this->migrateThemeMod('radius', 'radius_xs', 'field_603662f7a16f8');
        $this->migrateThemeMod('radius', 'radius_sm', 'field_6038fa31cfac6');
        $this->migrateThemeMod('radius', 'radius_md', 'field_6038fa400384b');
        $this->migrateThemeMod('radius', 'radius_lg', 'field_6038fa52576ba');

        $this->deleteThemeMod('radius');

        return true;
    }

    //Migrate header stuff.
    private function v_8($db) : bool
    {
        $this->migrateThemeMod('header', 'header_sticky', 'field_61434d3478ef7');
        $this->migrateThemeMod('header', 'header_background', 'field_61446365d1c7e');
        $this->migrateThemeMod('header', 'header_color', 'field_614467575de00');
        $this->migrateThemeMod('header', 'header_modifier', 'field_6070186956c15');
    
        $this->deleteThemeMod('header');

        return true;
    }

    //Migrate header.
    private function v_9($db) : bool
    {
        $this->migrateThemeMod('padding', 'main_content_padding', 'field_611e43ec4dfa5');
    
        $this->deleteThemeMod('padding');

        return true;
    }

    //Migrate quicklinks stuff.
    private function v_10($db) : bool
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
    private function v_11($db) : bool
    {
        $overlays = get_theme_mod('hero');

        $defaultColor = $overlays['field_614c713ae73ea']['field_614c7189e73eb'];
        $defaultOpacity = $overlays['field_614c713ae73ea']['field_614c7197e73ec'];

        $vibrantColor = $overlays['field_614c720fb65a4']['field_614c720fb65a5'];
        $vibrantOpacity = $overlays['field_614c720fb65a4']['field_614c720fb65a6'];

        $defaultOverlay = $this->hex2rgba($defaultColor, "0.".(int)$defaultOpacity);
        $vibrantOverlay = $this->hex2rgba($vibrantColor, "0.".(int)$vibrantOpacity);

        if ($vibrantColor||$defaultColor) {
            set_theme_mod('hero_overlay_enable', true);
        } else {
            set_theme_mod('hero_overlay_enable', false);
        }

        set_theme_mod('hero_overlay_neutral', $defaultOverlay);
        set_theme_mod('hero_overlay_vibrant', $vibrantOverlay);

        $this->deleteThemeMod('hero');

        return true;
    }

    //Migrate overlay stuff.
    private function v_12($db) : bool
    {
        $overlays = get_theme_mod('overlay');
        if ($overlays) {
            $color = $overlays['field_615c1bc3772c6']['field_615c1bc3780b0'];
            $opacity = $overlays['field_615c1bc3772c6']['field_615c1bc3780b6'];
            $overlay = $this->hex2rgba($color, "0.".(int)$opacity);

            var_dump();
            set_theme_mod('overlay', $overlay);
        }

        return true;
    }

    //Migrate modules stuff.
    private function v_13($db) : bool
    {

    //TODO: Must be granulary mapped to each to-field name
        $this->migrateThemeMod('posts', 'mod_posts_index_modifier', 'field_6061d864c6873');
        $this->migrateThemeMod('posts', 'mod_posts_list_modifier', 'field_6062fd67a2eb4');
        $this->migrateThemeMod('posts', 'mod_posts_expandablelist_modifier', 'field_60631bb52591c');
        $this->migrateThemeMod('contacts', 'mod_contacts_list_modifier', 'field_6063008d5068a');
        $this->migrateThemeMod('contacts', 'mod_contacts_card_modifier', 'field_6090f318a40ef');
        $this->migrateThemeMod('inlay', 'mod_inlay_list_modifier', 'field_606300da5068b');
        $this->migrateThemeMod('modules', 'mod_map_modifier', 'field_6063013a5068c');
        $this->migrateThemeMod('script', 'mod_script_modifier', 'field_6063072c25917');
        $this->migrateThemeMod('text', 'mod_text_modifier', 'field_60631b4025918');
        $this->migrateThemeMod('video', 'mod_video_modifier', 'field_60631b5f25919');
        $this->migrateThemeMod('index', 'mod_index_modifier', 'field_607843a6ba55e');
        $this->migrateThemeMod('localevent', 'mod_localevent_modifier', 'field_607ff0d6b8426');
        $this->migrateThemeMod('sectionssplit', 'mod_section_split_modifier', 'field_611f83757a727');

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
    private function v_14($db) : bool
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
     * A simple wrapper around set_theme_mod() in order to set a single property value of an associative array setting.
     * Key should include a dot in order to target a property. eg. color_palette.primary will target array('primary' => VALUE).
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
        $key = $parsedString[0] ?? '';
        $property = $parsedString[1] ?? '';

        if (empty($parsedString) ||empty($key)) {
            return false;
        }
    
        if (!empty($property)) {
            $associativeArr = get_theme_mod($key, []);
            $associativeArr = is_array($associativeArr) || $castToArray !== true ? $associativeArr : [];
      
            if (!is_array($associativeArr)) {
                $errorMessage = "Failed to migrate setting (" . $key . "." . $property . "). The specified setting already exists and is not an associative array.";
                var_dump($errorMessage);
                error_log(print_r($errorMessage, true));
                return false;
            }

            $associativeArr[$property] = $value;
            $value = $associativeArr;
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
            $output = 'rgba('.implode(",", $rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",", $rgb).')';
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
                wp_die(__('Current database version must be a number.', 'municipio'));
            }

            if ($currentDbVersion > $this->dbVersion) {
                wp_die(__('Database cannot be lower than currently installed (cannot downgrade).', 'municipio'));
            }

            //Fetch global wpdb object, save to $db
            $this->globalToLocal('wpdb', 'db');

            //Run upgrade(s)
            while ($currentDbVersion <= $this->dbVersion) {
                $currentDbVersion++;
                $funcName = 'v_' . (string) $currentDbVersion;
                if (method_exists($this, $funcName)) {
                    if ($this->{$funcName}($this->db)) {
                        update_option($this->dbVersionKey, (int) $currentDbVersion);
                        wp_cache_flush();
                    }
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
