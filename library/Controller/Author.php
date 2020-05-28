<?php

namespace Municipio\Controller;

/**
 * Class Author
 * @package Municipio\Controller
 */
class Author extends \Municipio\Controller\Archive
{
    public function init()
    {
        $this->data['postType'] = get_post_type();
        $this->data['template'] = !empty(get_field('archive_' . sanitize_title('author') . '_post_style', 'option')) ? get_field('archive_' . sanitize_title('author') . '_post_style', 'option') : 'collapsed';
        $this->data['posts'] = $this->getPosts();
        $this->data['paginationList'] = $this->preparePaginationObject();
        $this->data['queryParameters'] = $this->setQueryParameters();
        $this->data['taxonomies'] = $this->getTaxonomies();
        $this->data['archiveTitle'] = $this->getArchiveTitle();

    }

}
