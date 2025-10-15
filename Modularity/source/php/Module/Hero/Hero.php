<?php

namespace Modularity\Module\Hero;

use Modularity\Integrations\Component\ImageResolver;
use Modularity\Integrations\Component\ImageFocusResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;

class Hero extends \Modularity\Module
{
    public $slug = 'hero';
    public $supports = array();
    public $blockSupports = array(
        'align' => ['full']
    );

    public function init()
    {
        $this->nameSingular = __('Hero', 'modularity');
        $this->namePlural = __('Heros', 'modularity');
        $this->description = __('Outputs a hero', 'modularity');
    }

    public function data() : array
    {
        //Get module data
        $fields = $this->getFields();

        //Type
        $type = $fields['mod_hero_background_type'] ?? 'image';

        //Grab image
        if ('image' == $type) {
            $data = [
                'image' => ImageComponentContract::factory(
                    (int) $fields['mod_hero_background_image']['id'],
                    [1920, false],
                    new ImageResolver(),
                    new ImageFocusResolver($fields['mod_hero_background_image'])
                )
            ];
        }

        //Grab video
        if ('video' == $type) {
            $data = [
                'video' => $fields['mod_hero_background_video']
            ];
        }

        if(!isset($data['stretch'])) {
            $data['stretch'] = false;
        }

        //Custom hero data
        $customHeroData = [];
        if ($fields['mod_hero_display_as'] === 'callToActions') {
            $customHeroData['mediaFirst'] = $fields['mod_hero_media_first'] ?? false;
        }

        //Common fields
        $data['type']               = $type;
        $data['size']               = $fields['mod_hero_size'];
        $data['byline']             = $fields['mod_hero_byline'];
        $data['paragraph']          = $fields['mod_hero_body'];
        $data['backgroundType']     = $data['mod_hero_background_type'] ?? 'image';
        $data['heroView']           = !empty($fields['mod_hero_display_as']) ? $fields['mod_hero_display_as'] : 'default';
        $data['ariaLabel']          = __('Page hero section', 'modularity');
        $data['meta']               = !empty($fields['mod_hero_meta']) ? $fields['mod_hero_meta'] : false;
        $data['buttonArgs']         = $this->getButtonArgsFromFields($fields);
        $data['poster']             = $fields['mod_hero_poster_image']['sizes']['large'] ?? false;
        $data['customHeroData']     = $customHeroData;

        return $data;
    }

    /**
     * Get button args from fields
     * @param  array $fields
     * @return array|null
     */
    private function getButtonArgsFromFields(array $fields)
    {
        $buttonArgs = null;

        if (isset($fields['mod_hero_buttons']) && is_array($fields['mod_hero_buttons']) && !empty($fields['mod_hero_buttons'])) {
            $buttonArgs = [];
            foreach ($fields['mod_hero_buttons'] as $button) {
                if (is_array($button['link']) && !empty($button['link']['url']) && !empty($button['link']['title'])) {
                    $buttonArgs[] = [
                        'href' => $button['link']['url'],
                        'text' => $button['link']['title'],
                        'color' => $button['color'] ?? 'primary',
                        'style' => $button['type'] ?? 'filled',
                    ];
                }
            }
        }

        return $buttonArgs;
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
