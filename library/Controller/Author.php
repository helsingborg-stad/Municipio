<?php

namespace Municipio\Controller;

class Author extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $this->data['postType'] = 'author';
        $this->data['template'] = !empty(get_field('archive_author_post_style', 'option')) ? get_field('archive_author_post_style', 'option') : 'collapsed';
        $this->data['grid_size'] = !empty(get_field('archive_author_grid_columns', 'option')) ? get_field('archive_author_grid_columns', 'option') : 'grid-md-6';
    }
}
