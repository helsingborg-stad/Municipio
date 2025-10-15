<?php

namespace Modularity\Module\Modal;

use Municipio\Helper\Post;

class Modal extends \Modularity\Module
{
    public $slug = 'modal';
    public $supports = array();
    private $postType;

    public function init()
    {
        $this->nameSingular = __("Modal", 'modularity');
        $this->namePlural = __("Modals", 'modularity');
        $this->description = __("Outputs a button and the content of a selected Modal Content post into a modal, accessible by clicking on the button.", 'modularity');
        $this->postType = 'modal-content';

        // Only run if module is enabled
        if (in_array('mod-' . $this->slug, \Modularity\ModuleManager::getEnabled())) {
            add_action('init', [$this, 'registerPostType'], 99);
            add_filter('allowed_block_types_all', array($this, 'disallowBlockType'), 10, 2);
        }
    }

    public function data(): array
    {

        $fields = $this->getFields();
        $data   = [];

        // Modal button
        $data['buttonIcon']      = $fields['button']['material_icon'] ?? false;
        $data['buttonText']      = $fields['button']['text'] ?? false;
        $data['buttonSize']      = $fields['button']['size'] ?? 'md';
        $data['buttonStyle']     = $fields['button']['style'] ?? 'outlined';
        $data['buttonColor']     = $fields['button']['color'] ?? 'primary';
        $data['reversePosition'] = (bool) \intval($fields['button']['reverse_position']) ?? false;

        // Modal settings
        $data['useModalContentTitle'] = (bool) \intval($fields['modal']['use_modal_content_title']) ?? false;
        $data['modalIsPanel']         = (bool)  \intval($fields['modal']['is_panel']) ?? false;
        $data['modalSize']            = !empty($fields['modal']['size']) ? $fields['modal']['size'] : 'md';
        $data['modalPadding']         = !empty($fields['modal']['padding']) ? $fields['modal']['padding'] : 3;
        $data['modalBorderRadius']    = $fields['modal']['border_radius'] = 'md';

        // Modal content
        $modalContentPost = \Municipio\Helper\Post::preparePostObject(
            $fields['modal']['content']
        );
        $data['modalId']           = $modalContentPost->id ?? 0;
        $data['modalContentTitle'] = $modalContentPost->postTitleFiltered ?? false;
        $data['modalContent']      = $modalContentPost->postContentFiltered ?? false;

        return $data;
    }

    public function registerPostType()
    {
        $args = [
            'supports'              => [ 'title', 'editor', 'revisions' ],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 40,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'has_archive'           => false,
            'show_in_rest'          => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'page',
            'labels' => [
                'all_items'    => __('All Modal Contents', 'modularity'),
                'name'         => __('Modal Content', 'modularity'),
                'menu_name'    => __('Modal Content', 'modularity'),
                'add_new_item' => __('Add New Modal Content', 'modularity'),
                'add_new'      => __('Add Modal Content', 'modularity'),
                'new_item'     => __('New Modal Content', 'modularity'),
                'edit_item'    => __('Edit Modal Content', 'modularity'),
                'update_item'  => __('Update Modal Content', 'modularity'),
                'view_item'    => __('View Modal Content', 'modularity'),
                'view_items'   => __('View Modal Contents', 'modularity'),
                'search_items' => __('Search For Modal Content', 'modularity'),
            ],
            ];
        register_post_type($this->postType, $args);
    }

    /**
     * Disallow the Modal block for the current post type;
     * modals inside of modals can possibly create infinite loops.
     *
     * @param array $allowedBlockTypes The allowed block types.
     * @param object $editorContext The editor context.
     * @return array The updated allowed block types.
     */
    public function disallowBlockType($allowedBlockTypes, $editorContext)
    {
        if(isset($editorContext->post->post_type)) {
            if ($this->postType === $editorContext->post->post_type) {
                unset($allowedBlockTypes['acf/modal']);
            }
        }
        return $allowedBlockTypes;
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
