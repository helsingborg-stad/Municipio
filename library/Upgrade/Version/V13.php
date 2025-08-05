<?php

namespace Municipio\Upgrade\Version;

use Municipio\Upgrade\Version\Helper\DeleteThemeMod;
use Municipio\Upgrade\Version\Helper\MigrateThemeMod;

class V13 implements \Municipio\Upgrade\VersionInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        if (get_theme_mod('site')) {
            MigrateThemeMod::migrate('site', 'header_modifier', 'field_6070186956c15');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'header_modifier', 'field_6070186956c15');
        }

        if (get_theme_mod('posts')) {
            MigrateThemeMod::migrate('posts', 'mod_posts_index_modifier', 'field_6061d864c6873');
            MigrateThemeMod::migrate('posts', 'mod_posts_list_modifier', 'field_6062fd67a2eb4');
            MigrateThemeMod::migrate('posts', 'mod_posts_expandablelist_modifier', 'field_60631bb52591c');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'mod_posts_index_modifier', 'field_6061d864c6873');
            MigrateThemeMod::migrate('modules', 'mod_posts_list_modifier', 'field_6062fd67a2eb4');
            MigrateThemeMod::migrate('modules', 'mod_posts_expandablelist_modifier', 'field_60631bb52591c');
        }

        if (get_theme_mod('contacts')) {
            MigrateThemeMod::migrate('contacts', 'mod_contacts_list_modifier', 'field_6063008d5068a');
            MigrateThemeMod::migrate('contacts', 'mod_contacts_card_modifier', 'field_6090f318a40ef');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'mod_contacts_list_modifier', 'field_6063008d5068a');
            MigrateThemeMod::migrate('modules', 'mod_contacts_card_modifier', 'field_6090f318a40ef');
        }

        if (get_theme_mod('inlay')) {
            MigrateThemeMod::migrate('inlay', 'mod_inlay_list_modifier', 'field_606300da5068b');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'mod_inlay_list_modifier', 'field_606300da5068b');
        }

        MigrateThemeMod::migrate('modules', 'mod_map_modifier', 'field_6063013a5068c');

        if (get_theme_mod('script')) {
            MigrateThemeMod::migrate('script', 'mod_script_modifier', 'field_6063072c25917');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'mod_script_modifier', 'field_6063072c25917');
        }

        if (get_theme_mod('text')) {
            MigrateThemeMod::migrate('text', 'mod_text_modifier', 'field_60631b4025918');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'mod_text_modifier', 'field_60631b4025918');
        }

        if (get_theme_mod('video')) {
            MigrateThemeMod::migrate('video', 'mod_video_modifier', 'field_60631b5f25919');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'mod_video_modifier', 'field_60631b5f25919');
        }

        if (get_theme_mod('index')) {
            MigrateThemeMod::migrate('index', 'mod_index_modifier', 'field_607843a6ba55e');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'mod_index_modifier', 'field_607843a6ba55e');
        }

        if (get_theme_mod('localevent')) {
            MigrateThemeMod::migrate('localevent', 'mod_localevent_modifier', 'field_607ff0d6b8426');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'mod_localevent_modifier', 'field_607ff0d6b8426');
        }

        if (get_theme_mod('sectionssplit')) {
            MigrateThemeMod::migrate('sectionssplit', 'mod_section_split_modifier', 'field_611f83757a727');
        } elseif (get_theme_mod('modules')) {
            MigrateThemeMod::migrate('modules', 'mod_section_split_modifier', 'field_611f83757a727');
        }

        DeleteThemeMod::delete('modules');
        DeleteThemeMod::delete('site');
        DeleteThemeMod::delete('posts');
        DeleteThemeMod::delete('contacts');
        DeleteThemeMod::delete('index');
        DeleteThemeMod::delete('inlay');
        DeleteThemeMod::delete('script');
        DeleteThemeMod::delete('localevent');
        DeleteThemeMod::delete('sectionssplit');
        DeleteThemeMod::delete('text');
        DeleteThemeMod::delete('video');
        DeleteThemeMod::delete('card');
    }
}