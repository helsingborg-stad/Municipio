<?php

namespace Modularity\Module\Text;

class Text extends \Modularity\Module
{
    public $slug = 'text';
    public $supports = array('editor');

    public function init()
    {
        $this->nameSingular = __('Text', 'modularity');
        $this->namePlural = __('Texts', 'modularity');
        $this->description = __('Outputs text', 'modularity');
    }

    public function data() : array
    {
        $data = $this->getFields() ?? []; 

        // Post content [with multiple fallbacks]
        $data['postContent'] = $this->data['post_content'] ?? $data['post_content'] ?: $data['content'] ?? '';

        //Run relevant filters
        foreach(['Modularity/Display/SanitizeContent', 'the_content'] as $filter) {
            $data['postContent'] = apply_filters($filter, $data['postContent']);
        }

        // Check if content contains h1-h6 tags
        $data['hasHeadingsInContent'] = preg_match('/<h[1-6]/', $data['post_content'] ?? '');

        // Alway set ID
        $data['ID'] = $data['ID'] ?? uniqid();

        // Set default values
        return $data ?? []; 
    }
    
    public function template()
    {
        if (!isset($this->data['hide_box_frame']) || !$this->data['hide_box_frame']) {
            return 'box.blade.php';
        }
        return 'article.blade.php';
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
