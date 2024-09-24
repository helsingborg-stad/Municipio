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
        // echo '<pre>' . print_r($this->data, true) . '</pre>';
        // die();
    }
}
