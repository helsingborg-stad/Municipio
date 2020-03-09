<?php

namespace Municipio\Controller;

class Archive extends \Municipio\Controller\BaseController
{
    private static $gridSize;

    private static $randomGridBase = array();
    private static $gridRow = array();
    private static $gridColumns = array();

    public function init()
    {
        $this->data['posts'] = $this->getArchivePosts();
        $this->data['postType'] = get_post_type();
        $this->data['template'] = !empty(get_field('archive_' . sanitize_title($this->data['postType']) . '_post_style', 'option')) ? get_field('archive_' . sanitize_title($this->data['postType']) . '_post_style', 'option') : 'collapsed';
        $this->data['paginationList'] = $this->prepareArchivePagination();

        
        /*
        $postType = get_post_type();
        if (is_author()) {
            $postType = 'author';
            $this->data['hasLeftSidebar'] = true;
        }

        $this->data['postType'] = $postType;
        $this->data['template'] = !empty(get_field('archive_' . sanitize_title($postType) . '_post_style', 'option')) ? get_field('archive_' . sanitize_title($postType) . '_post_style', 'option') : 'collapsed';
        $this->data['grid_size'] = !empty(get_field('archive_' . sanitize_title($postType) . '_grid_columns', 'option')) ? get_field('archive_' . sanitize_title($postType) . '_grid_columns', 'option') : 'grid-md-6';

        $this->data['grid_alter'] = get_field('archive_' . sanitize_title($postType) . '_grid_columns_alter', 'option') ? true : false;
        $this->data['gridSize'] = (int)str_replace('-', '', filter_var($this->data['grid_size'], FILTER_SANITIZE_NUMBER_INT));
        self::$gridSize = $this->data['gridSize'];

        if ($this->data['grid_alter']) {
            $this->gridAlterColumns();
        }

        add_filter('archive_equal_container', array($this, 'setEqualContainer'), 8, 3);

        */ 
    }
    private function prepareArchivePagination(){
        $pages = [];
        $this->globalToLocal('wp_query', 'wp_query');
    
        for($archivePage = 1; $archivePage <= (int)$this->wp_query->max_num_pages; $archivePage++) {
            $pages[] = array(
                'label' => $archivePage,
                'href' => str_replace('https://' . $_SERVER['SERVER_NAME'], '', get_pagenum_link($archivePage))
            );
        }

        return \apply_filters('Municipio/Controller/Archive/prepareArchivePagination', $pages);
    }

    private function getArchivePosts()
    {
        $preparedPosts = [];
        $this->globalToLocal('posts', 'posts');

        if(is_array($this->posts) && !empty($this->posts)) {
            foreach($this->posts as $post) {
                $post->href = $post->permalink;

                if(get_the_post_thumbnail_url($post->ID)){
                    $post->featuredImage = \get_the_post_thumbnail_url($post->id);
                }else{
                    $post->featuredImage = null;
                }
                $preparedPosts[] = \Municipio\Helper\Post::preparePostObject($post);
            }

            return \apply_filters('Municipio/Controller/Archive/getArchivePosts', $preparedPosts);
        }
    }


    public function setEqualContainer($equalContainer, $postType, $template)
    {
        $templatesWithEqualContainer = array('cards');

        if (in_array($template, $templatesWithEqualContainer)) {
            $equalContainer = true;
        }

        return $equalContainer;
    }

    public function gridAlterColumns()
    {
        $gridRand = array();

        switch ($this->data['gridSize']) {
            case 12:
                $gridRand = array(
                    array(12)
                );
                break;

            case 6:
                $gridRand = array(
                    array(12),
                    array(6, 6),
                    array(6, 6)
                );
                break;

            case 4:
                $gridRand = array(
                    array(8, 4),
                    array(4, 4, 4),
                    array(4, 8)
                );
                break;

            case 3:
                $gridRand = array(
                    array(6, 3, 3),
                    array(3, 3, 3, 3),
                    array(3, 3, 6),
                    array(3, 3, 3, 3),
                    array(3, 6, 3)
                );
                break;

            default:
                $gridRand = array(
                    array(12)
                );
                break;
        }

        self::$randomGridBase = $gridRand;
    }

    public static function getColumnSize()
    {
        // Fallback if not set
        if (empty(self::$randomGridBase)) {
            return 'grid-md-' . self::$gridSize;
        }

        if (empty(self::$gridRow)) {
            self::$gridRow = self::$randomGridBase;
        }

        if (empty(self::$gridColumns)) {
            self::$gridColumns = self::$gridRow[0];
            array_shift(self::$gridRow);
        }

        $columnSize = 'grid-md-' . self::$gridColumns[0];
        array_shift(self::$gridColumns);

        return $columnSize;
    }

    public static function getColumnHeight()
    {
        switch (self::$gridSize) {
            case 3:
                return '280px';

            case 4:
                return '400px';

            case 6:
                return '500px';

            case 12:
                return '500px';

            default:
                return false;
        }

        return false;
    }
}
