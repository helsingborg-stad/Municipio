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
        $this->data['postType'] = get_post_type();
        $this->data['template'] = !empty(get_field('archive_' . sanitize_title($this->data['postType']) . '_post_style', 'option')) ? get_field('archive_' . sanitize_title($this->data['postType']) . '_post_style', 'option') : 'collapsed';
        $this->data['posts'] = $this->getPosts();
        $this->data['paginationList'] = $this->getPagination();
        $this->data['queryParameters'] = $this->setQueryParameters();
        $this->data['taxonomies'] = $this->getTaxonomies(); 
        $this->data['archiveTitle'] = $this->getArchiveTitle();
    }

    protected function getArchiveTitle()
    {
        $title = \ucfirst($this->data['postType']);

        return \apply_filters('Municipio/Controller/Archive/getArchiveTitle', $title);  
    }

    protected function getPostTerms($postID)
    {
        $terms = wp_get_post_terms($postID);
        $taxonomies = get_taxonomies('', 'names');
        $termsList = [];

        foreach($taxonomies as $taxonomy) {
            $terms = wp_get_post_terms($postID, $taxonomy);

            if(!empty($terms)){

                foreach($terms as $term){
                    $termsList[] = [
                        'label' => $term->name,
                        'href' => get_term_link( $term->term_id )
                    ];
                }

            }

        }

        return \apply_filters('Municipio/Controller/Archive/getArchiveTitle', $termsList);  
    }

    protected function getPagination(){
        global $wp_query;
        $pagination = [];
        $numberOfPages = $wp_query->max_num_pages + 1;
        $archiveUrl = get_post_type_archive_link($this->data['postType']);
        $href = '';
        $currentPage = (get_query_var('paged')) ? get_query_var('paged') : 1;

        if($numberOfPages > 1){
            for($i = 1; $i < $numberOfPages; $i++){

                $href = $archiveUrl . '?' . $this->setQueryString($i);
    
                $pagination[] = array(
                    'href' => $href,
                    'label' => (string) $i
                );
            }
        }
        
        return \apply_filters('Municipio/Controller/Archive/prepareSearchResultObject', $pagination); 
    }

    protected function setQueryString($number) {
        parse_str($_SERVER['QUERY_STRING'],$queryArgList);
        $queryArgList['paged'] = $number;
        $queryString = http_build_query($queryArgList) . "\n";

        return \apply_filters('Municipio/Controller/Archive/setQueryString', $queryString); 
    }

    protected function setQueryParameters() 
    {
        $queryParameters = [
            'search' =>  isset($_GET['s']) ? $_GET['s'] : '',
            'from' =>  isset($_GET['from']) ? $_GET['from'] : '',
            'to' =>  isset($_GET['to']) ? $_GET['to'] : ''
        ];

        return \apply_filters('Municipio/Controller/Archive/setQueryParameters', 
                (object) $queryParameters);
    }

    protected function getTaxonomies() 
    {
        $taxonomies = get_object_taxonomies($this->data['postType']);
        $taxonomiesList = [];
        
        foreach($taxonomies as $taxonomy){
            $text = str_replace('-',' ',$taxonomy);
            $currentTerm = null;
            $terms = get_terms( 
                array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false
                ) 
            );

            if(isset($_GET['filter'][$taxonomy])){
                $currentTerm = get_term_by('slug', $_GET['filter'][$taxonomy], $taxonomy);
            }
            
            $taxonomiesList[$text]['currentSlug'] = (isset($currentTerm)) ? $currentTerm->name : $text;
            $taxonomiesList[$text]['categories'][] = ['text' => $taxonomy, 'link' => "filter[{$taxonomy}]=delete"];


            foreach($terms as $term){
                $taxonomiesList[$text]['categories'][] = ['text' => $term->name, 'link' => "filter[{$taxonomy}]={$term->slug}"];
            }
            
        }

        return \apply_filters('Municipio/Controller/Archive/getTaxonomies', $taxonomiesList);
    }

    protected function getPosts()
    {        
        
        $this->globalToLocal('posts', 'posts');
        $template = $this->data['template'];
        $items    = null;
        if(is_array($this->posts) && !empty($this->posts)) {

            if ($template == 'list') {
                $items = $this->getListItems($this->posts);
            } else{
                $items = $this->getItems($this->posts);
            } 

            return \apply_filters('Municipio/Controller/Archive/getArchivePosts', $items);
        }
        
    }

    protected function getItems($posts)
    {
        $preparedPosts = [];
        
        foreach($posts as $post) {
          
            $post = \Municipio\Helper\Post::preparePostObject($post);
            $post->href = get_permalink($post->id);
            $post->featuredImage = $this->getFeaturedImage($post);
            $post->excerpt =  wp_trim_words($post->postContent, 15);
            $post->postDate = \date('Y-m-d', strtotime($post->postDate));
            $post->postModified = \date('Y-m-d', strtotime($post->postModified));
            $post->terms = $this->getPostTerms($post->id);

            $preparedPosts[] = $post;
        }
        
        return \apply_filters('Municipio/Controller/Archive/getItems', $preparedPosts);
    }

    protected function getListItems($posts)
    {
        
        $preparedPosts = [
            'items' => [],
            'headings' => ['Title', 'Published', 'Updated']
        ];

        foreach($posts as $post) {
            $post = \Municipio\Helper\Post::preparePostObject($post);
            $postDate = \date('Y-m-d', strtotime($post->postDate));
            $postModified = \date('Y-m-d', strtotime($post->postModified));

            $preparedPosts['items'][] = 
            [
                'href' => get_permalink($post->id),
                'columns' => [
                    $post->postTitle,
                    $post->post_date = $postDate,
                    $post->post_modified = $postModified
                ]
                
            ];
        }
        
        return \apply_filters('Municipio/Controller/Archive/getListItems', $preparedPosts);
    }

    protected function getFeaturedImage($post) 
    {
        $featuredImageID = get_post_thumbnail_id();
        $featuredImageSRC = \get_the_post_thumbnail_url($post->id);
        $featuredImageAlt = get_post_meta($featuredImageID, '_wp_attachment_image_alt', TRUE);
        $featuredImageTitle = get_the_title($featuredImageID);

        $featuredImage = [
            'src' => $featuredImageSRC ? $featuredImageSRC : null,
            'alt' => $featuredImageAlt ? $featuredImageAlt : null,
            'title' => $featuredImageTitle ? $featuredImageTitle : null
        ];

        return \apply_filters('Municipio/Controller/Archive/getFeaturedImage', $featuredImage);
        
    }

    protected function getParentPost($postID) 
    {
        $parentPostID = wp_get_post_parent_id( $postID );
        $parentPost = get_post($parentPostID);

        return apply_filters( "Municipio/Controller/Archive/getParentPost", $parentPost);
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
