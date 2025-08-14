<?php

namespace Municipio\Controller;

/**
 * 404 controller
 */
class E404 extends \Municipio\Controller\BaseController
{
    public $query;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Wrapper class
        $wrapperClasses = ['t-404'];

        //Get local instance of wp_query
        $this->globalToLocal('wp_query', 'query');

        //Get current post type to view
        $this->data['postType'] = $this->getRequestedPostType();

        //Get archive link to view
        $this->data['archiveLink'] = $this->getPostTypeArchivePermalink();

        //Content
        $this->data['heading']    = $this->getHeading();
        $this->data['subheading'] = $this->getSubheading();

        //Actions
        $shouldLinkToArchive = $this->getPostTypeArchivePermalink() !== null ? [
            'label' => __("Show all", 'municipio'),
            'href'  => $this->data['archiveLink']
        ] : [];

        $actionButtons = isset($this->data['customizer']->error404Buttons)
            ? $this->data['customizer']->error404Buttons
            : array_keys(\Municipio\Customizer\Sections\ErrorPages::getButtonChoices('404'));

        $this->data['actionButtons'] = [];
        if (in_array('return', $actionButtons)) {
            $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getReturnButton($shouldLinkToArchive);
        }
        if (in_array('home', $actionButtons)) {
            $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getHomeButton();
        }

        //Image
        $this->data['image'] = isset($this->data['customizer']->error404Image) && !empty($this->data['customizer']->error404Image)
            ? $this->data['customizer']->error404Image
            : false;

        // Backdrop
        $backdrop = isset($this->data['customizer']->error404Backdrop) 
            ? $this->data['customizer']->error404Backdrop
            : true;

        // Extra wrapper classes
        if ($this->data['image']) {
            $wrapperClasses[] = 't-404--has-image';
        }
        if ($backdrop) {
            $wrapperClasses[] = 't-404--has-error-backdrop';
        }
        $wrapperClasses = implode(' ', $wrapperClasses);
        $this->data['wrapperClasses'] = $this->wpService->applyFilters('Municipio/404/WrapperClasses', $wrapperClasses);
    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading()
    {
        $heading = isset($this->data['customizer']->error404Heading) && !empty($this->data['customizer']->error404Heading)
            ? $this->data['customizer']->error404Heading
            : \Municipio\Customizer\Sections\ErrorPages::getDefaultHeading('404');
        return $this->wpService->applyFilters('Municipio/404/Heading', $this->wpService->__($heading, 'municipio'), $this->getRequestedPostType());
    }

    /**
     * Returns the body
     * @return string
     */
    protected function getSubheading()
    {
        $subheading = isset($this->data['customizer']->error404Description) && !empty($this->data['customizer']->error404Description)
            ? $this->data['customizer']->error404Description
            : \Municipio\Customizer\Sections\ErrorPages::getDefaultDescription('404');
        return str_replace("%s", $this->getRequestedPostType(), $this->wpService->applyFilters('Municipio/404/Body', 
            $this->wpService->__($subheading, 'municipio'), 
            ucfirst($this->getRequestedPostType()))
        );
    }

    /**
     * Returns the posttype requested, if post found, default to post.
     * @return string
     */
    private function getRequestedPostType()
    {
        //Get queried posttype
        if (isset($this->query->query) && isset($this->query->query['post_type'])) {
            $postType = $this->query->query['post_type'];
        }

        //Default to page if not set
        if (!isset($postType) || is_null($postType)) {
            $postType = $this->wpService->__("post");
        }

        return $this->wpService->applyFilters('Municipio/404/PostType', $postType);
    }

    /**
     * Returns link to archive or null, if not found
     * @return void
     */
    private function getPostTypeArchivePermalink()
    {
        return $this->wpService->applyFilters(
            'Municipio/404/ArchivePermalink',
            !is_null($this->getRequestedPostType()) && $this->getRequestedPostType() != "post" ? $this->wpService->getPostTypeArchiveLink($this->getRequestedPostType()) : null
        );
    }
}
