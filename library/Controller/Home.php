<?php

namespace Municipio\Controller;

class Home extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $this->data['grid_size'] = !empty(get_field('blog_grid_columns', 'option')) ? get_field('blog_grid_columns', 'option') : 'grid-md-6';
    }
}
