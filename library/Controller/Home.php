<?php

namespace Municipio\Controller;

class Home extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $this->data['enabledFilters'] = get_field('archive_' . sanitize_title(get_post_type()) . '_post_filters', 'option');
        $this->data['grid_size'] = !empty(get_field('blog_grid_columns', 'option')) ? get_field('blog_grid_columns', 'option') : 'grid-md-6';
    }

    public function doPostFiltering($where)
    {
        var_dump($where);
        return $where;
    }
}
