<?php

declare(strict_types=1);

namespace Modularity\Module\Text;

use Modularity\Helper\WpService;

class Text extends \Modularity\Module
{
    public $slug = 'text';
    public $supports = ['editor'];

    public function init()
    {
        $this->nameSingular = __('Text', 'municipio');
        $this->namePlural = __('Texts', 'municipio');
        $this->description = __('Outputs text', 'municipio');
    }

    public function data(): array
    {
        $data = $this->getFields() ?? [];

        // Post content [with multiple fallbacks]
        $data['postContent'] = $data['post_content'] ?? '';
        $data['postContent'] = $data['postContent'] ?: $data['content'] ?? '';

        // If content is still empty and ID is set, try to fetch content from post
        if ($this->ID) {
            $data['postContent'] = $data['postContent'] ?: get_post($this->ID)->post_content ?? '';
        }

        //Run relevant filters
        foreach (['Modularity/Display/SanitizeContent', 'the_content'] as $filter) {
            $data['postContent'] = WpService::get()->applyFilters($filter, $data['postContent']);
        }

        // Check if content contains h1-h6 tags
        $data['hasHeadingsInContent'] = preg_match('/<h[1-6]/', $data['postContent'] ?? '');

        // Alway set ID
        $data['ID'] ??= uniqid();

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
