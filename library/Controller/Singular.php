<?php

namespace Municipio\Controller;

/**
 * Class Singular
 * @package Municipio\Controller
 */
class Singular extends \Municipio\Controller\BaseController
{
    public function init()
    {

        //Get post data 
        $this->data['post'] = \Municipio\Helper\Post::preparePostObject(get_post());

        //Get feature image data
        $this->data['feature_image'] = $this->getFeatureImage($this->data['post']->id);

        //Get Author data
        $this->data['authorName'] = $this->getAuthor($this->data['post']->id)->name;
        $this->data['authorAvatar'] = $this->getAuthor($this->data['post']->id)->avatar;

        //Get published data
        $this->data['publishedDate'] = $this->getPostDates($this->data['post']->id)->published;
        $this->data['updatedDate'] = $this->getPostDates($this->data['post']->id)->updated;

        $this->data['publishTranslations'] = array(
            'updated'   => __('Last updated', 'municipio'),
            'published' => __('Published date', 'municipio'),
            'by'        => __('Published by', 'municipio'),
            'on'        => __('on', 'municipio'),
        );

        //Comments
        $this->data['comments'] = get_comments(array(
            'post_id'   => $this->data['post']->id,
            'order'     => get_option('comment_order')
        ));

        //Replies
        $this->data['replyArgs'] = array(
            'add_below'  => 'comment',
            'respond_id' => 'respond',
            'reply_text' => __('Reply'),
            'login_text' => __('Log in to Reply'),
            'depth'      => 1,
            'before'     => '',
            'after'      => '',
            'max_depth'  => get_option('thread_comments_depth')
        );



        //Post settings
        $this->data['settingItems'] = apply_filters_deprecated('Municipio/blog/post_settings', array($this->data['post']), '3.0', 'Municipio/blog/postSettings'); 

        //Should link author page
        $this->data['authorPages'] = apply_filters('Municipio/author/hasAuthorPage', false);
    }

    /**
     * @param $id
     * @return object
     */
    private function getAuthor($id): object
    {
        $author = array();
        $author['name'] = get_the_author_meta( 'display_name', $this->data['post']->postAuthor );  
        $author['avatar'] = get_avatar_url($id);

        return apply_filters('Municipio/Controller/Singular/author', (object) $author);
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getPostDates($id)
    {
        return apply_filters('Municipio/Controller/Singular/publishDate', (object) [
            'published' => get_the_date(), 
            'updated' => get_the_modified_date()
        ]);
    }

    /**
     * @param $id Post id
     * @param $size Name or array for size of image
     * @return array An array of data related to the image
     */
    private function getFeatureImage($id, $size = [1920,1080])
    {
        $image_id = get_post_thumbnail_id($id);

        if (!$image_id) return false;

        $featuredImageObject = (object) [
            'id' => get_post_thumbnail_id($id), 
            'src' => wp_get_attachment_image_src($image_id, $size),
            'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', TRUE),
            'title' => get_the_title($image_id)
        ];
         
        return apply_filters('Municipio/Controller/Singular/featureImage', $featuredImageObject);
    }
}
