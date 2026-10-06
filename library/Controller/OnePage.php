<?php

namespace Municipio\Controller;

/**
 * Class OnePage
 * @package Municipio\Controller
 */
class OnePage extends \Municipio\Controller\Singular
{
    /**
     * @return array|void
     */
    public function init()
    {
        parent::init();

        $this->data['shouldRenderPostContent'] = $this->shouldRenderPostContent();

        return $this->data;
    }

    /**
     * Determine whether the one-page template should render post content.
     */
    public function shouldRenderPostContent(): bool
    {
        return !empty($this->data['post']);
    }
}
