<?php

namespace Modularity\Module\InlayList;

class InlayList extends \Modularity\Module
{
    public $slug = 'inlaylist';
    public $supports = array();
    public $isBlockCompatible = false;

    public function init()
    {
        $this->nameSingular = __("Inlay List", 'modularity');
        $this->namePlural = __("Inlay Lists", 'modularity');
        $this->description = __("Outputs one or more posts from selected post-type.", 'modularity');

        add_filter('acf/fields/post_object/result/name=link_internal', array($this, 'acfLocationSelect'), 10, 4);
    }

    public function data(): array
    {
        $data = array();
        $data['ID'] = $this->ID;
        $data['items'] = $this->buildListItems(get_field('items', $this->ID));
        $data['iconLast'] = get_field('icon_last', $this->ID);
        $data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $this->post_type, $this->args));
        
        return $data;
    }

    /**
     * Adding address to Location select box
     * @param  string   $title    the text displayed for this post object
     * @param  object   $post     the post object
     * @param  array    $field    the field array containing all attributes & settings
     * @param  int      $post_id  the current post ID being edited
     * @return string             updated title
     */
    public function acfLocationSelect($title, $post, $field, $post_id)
    {
        $address = get_permalink($post->ID, false);

        if (!empty($address)) {
            $title .= '<br/><span class="inlay-list-url-helper"> ( ' . str_replace(home_url(), "", $address) . ' ) </span>';
        }

        return $title;
    }

    /**
     * Prepare list items to support list component
     *
     * @param $field
     * @return object $list List data prepared for view
     */
    public function buildListItems($field)
    {
        (object) $list = [];

        foreach ($field as $item) {
            $item = (object) $item;

            if (empty($item->title) && ! empty($item->titel)) {
                $item->title = $item->titel;
            }

            if ($item->type === 'internal') {
                $label = $item->title ? $item->title : $item->link_internal->post_title;

                if ($item->date === true) {
                    $label .= " - " . wp_date(\Modularity\Helper\Date::getDateFormat('date'), strtotime($item->link_internal->post_date));
                }

                $list[] = [
                    'label' => $label,
                    'href' => $item->link_internal->post_type === "attachment" ? wp_get_attachment_url($item->link_internal->ID) : get_permalink($item->link_internal->ID),
                    'external' => false,
                ];
            }

            if ($item->type === 'external') {
                $list[] = [
                    'label' => !empty(trim($item->titel)) ? $item->titel : $item->title,
                    'href' => $item->link_external,
                    'external' => true,
                ];
            }
        }

        return $list;
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only en module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
