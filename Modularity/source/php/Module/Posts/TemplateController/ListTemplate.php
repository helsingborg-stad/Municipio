<?php

namespace Modularity\Module\Posts\TemplateController;

use Modularity\Module\Posts\Helper\DomainChecker;

/**
 * Class ListTemplate
 * @package Modularity\Module\Posts\TemplateController
 */
class ListTemplate extends AbstractController
{
    protected $args;
    protected DomainChecker $domainChecker;

    public $data = [];

    /**
     * ListTemplate constructor.
     * @param \Modularity\Module\Posts\Posts $module Instance of the Posts module.
     * @param array $args Arguments passed to the template controller
     * @param array $data Data to be used in the template
     * @param object $fields Object containing ACF fields
     */
    public function __construct(\Modularity\Module\Posts\Posts $module)
    {
        $this->args = $module->args;
        $this->data = $module->data;
        $this->domainChecker = $module->domainChecker;
        $this->fields = $module->fields;
        $this->module = $module;
        $this->data['posts'] = $this->prepareList([
            'posts_data_source' => $this->data['posts_data_source'] ?? '',
            'archive_link' => $this->data['archiveLink'] ?? '',
            'archive_link_url' => $this->data['archiveLinkUrl'] ?? '',
            'filters' => $this->data['filters'] ?? '',
        ]);
    }

    /**
     * @param array $posts array of posts
     * @param array $postData array of data settings
     * @return array
     */
    public function prepareList(array $postData)
    {
        $posts = [];
        if (!empty($this->data['posts']) && is_array($this->data['posts'])) {
            $this->data['posts'] = $this->preparePosts($this->module);
            foreach ($this->data['posts'] as $post) {
                
                if ($post->getPostType() === 'attachment') {
                    $post->permalink = wp_get_attachment_url($post->getId());
                }

                $post->icon      = 'arrow_forward';
                $post->classList = $post->classList ?? [];
                $post->attributeList = ['data-js-item-id' => $post->getId()]; 

                if (
                    !empty($this->fields['posts_open_links_in_new_tab']) &&
                    !$this->domainChecker->isSameDomain($post->getPermalink())
                ) {
                    $post->attributeList['target'] = '_blank';
                }

                if(boolval(($this->data['meta']['use_term_icon_as_icon_in_list'] ?? false))) {
                    $post->icon = $post->getIcon()?->toArray() ?: 'arrow_forward';
                }

                $posts[] = $post;
            }
        }

        return $posts;
    }
}
