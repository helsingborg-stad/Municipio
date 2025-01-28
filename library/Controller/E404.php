<?php

namespace Municipio\Controller;

class E404 extends \Municipio\Controller\BaseController
{
    public $query;

    public function init()
    {
        parent::init();

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

        $this->data['actionButtons']   = [];
        $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getReturnButton($shouldLinkToArchive);
        $this->data['actionButtons'][] = \Municipio\Controller\Error\Buttons::getHomeButton();
    }

    /**
     * Returns the heading
     * @return  string
     */
    protected function getHeading()
    {
        return $this->wpService->applyFilters('Municipio/404/Heading', $this->wpService->__("Oops! The page you requested cannot be found.", 'municipio'), $this->getRequestedPostType());
    }

    /**
     * Returns the body
     * @return string
     */
    protected function getSubheading()
    {
        return str_replace("%s", $this->getRequestedPostType(), $this->wpService->applyFilters('Municipio/404/Body', $this->wpService->__("The %s you are looking for is either moved or removed.", 'municipio'), $this->getRequestedPostType()));
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
