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

        $this->data['category']   = wp_get_post_terms($this->data['post']->id, 'meta_category')[0]->name ?? null;
        $this->data['technology'] = wp_get_post_terms($this->data['post']->id, 'meta_technology')[0]->name ?? null;
        $this->data['status']     = wp_get_post_terms($this->data['post']->id, 'meta_status')[0]->name ?? null;
        $this->data['department'] = wp_get_post_terms($this->data['post']->id, 'department')[0]->name ?? null;
        $this->data['progress']   = get_post_meta($this->data['post']->id, 'progress', true) ?? null;
        $this->data['imageUrl']   = get_the_post_thumbnail_url($this->data['post']->id) ?: null;

        $this->appendToLangObject();
        $this->setInformationListData();
    }

    /**
     * Appends translated strings to the language object.
     */
    private function appendToLangObject(): void
    {
        $this->data['lang']->information  = __('Information', 'municipio');
        $this->data['lang']->status       = __('Status', 'municipio');
        $this->data['lang']->department   = __('Department', 'municipio');
        $this->data['lang']->category     = __('Category', 'municipio');
        $this->data['lang']->technologies = __('Technologies', 'municipio');
        $this->data['lang']->contact      = __('Contact', 'municipio');
    }

    /**
     * Sets the information list data for the project.
     */
    private function setInformationListData(): void
    {
        $this->data['informationList'] = [];

        if (!empty($this->data['department'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->department,
                'value' => $this->data['department']
            ];
        }

        if (!empty($this->data['category'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->category,
                'value' => $this->data['category']
            ];
        }

        if (!empty($this->data['technology'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->technologies,
                'value' => $this->data['technology']
            ];
        }

        if (!empty($this->data['post']->schemaObject['employee']['alternateName'])) {
            $this->data['informationList'][] = [
                'label' => $this->data['lang']->contact,
                'value' => $this->data['post']->schemaObject['employee']['alternateName']
            ];
        }
    }
}
