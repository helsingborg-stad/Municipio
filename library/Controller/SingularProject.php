<?php

namespace Municipio\Controller;

/**
 * Class SingularJobPosting
 */
class SingularProject extends \Municipio\Controller\Singular
{
    protected object $postMeta;
    public string $view = 'single-schema-project';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->data['category']   = wp_get_post_terms($this->data['post']->id, '@meta.category')[0]->name ?? null;
        $this->data['technology'] = wp_get_post_terms($this->data['post']->id, '@meta.technology')[0]->name ?? null;
        $this->data['status']     = wp_get_post_terms($this->data['post']->id, '@meta.status')[0]->name ?? null;
        $this->data['department'] = wp_get_post_terms($this->data['post']->id, 'department')[0]->name ?? null;
        $this->data['progress']   = get_post_meta($this->data['post']->id, 'progress', true) ?? null;
        $this->data['imageUrl']   = 'https://picsum.photos/600/337';

        $this->appendToLangObject();
        $this->setInformationListData();
    }

    private function appendToLangObject(): void
    {
        $this->data['lang']->information  = __('Information', 'municipio');
        $this->data['lang']->status       = __('Status', 'municipio');
        $this->data['lang']->progress     = __('Progress', 'municipio');
        $this->data['lang']->department   = __('Department', 'municipio');
        $this->data['lang']->category     = __('Category', 'municipio');
        $this->data['lang']->technologies = __('Technologies', 'municipio');
        $this->data['lang']->contact      = __('Contact', 'municipio');
    }

    private function setInformationListData(): void
    {
        $this->data['informationList'] = [
            [
                'label' => $this->data['lang']->department,
                'value' => $this->data['department']
            ],
            [
                'label' => $this->data['lang']->category,
                'value' => $this->data['category']
            ],
            [
                'label' => $this->data['lang']->technologies,
                'value' => $this->data['technology']
            ],
            [
                'label' => $this->data['lang']->contact,
                'value' => $this->data['post']->schemaObject['employee']['alternateName']
            ]
        ];
    }
}
