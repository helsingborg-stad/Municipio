<?php

namespace Modularity\Module\Posts\TemplateController;

use Modularity\Helper\WpService as WpServiceHelper;
use Modularity\Module\Posts\Helper\Column as ColumnHelper;
use Modularity\Module\Posts\Helper\DomainChecker;
use WP_Post;
use WpService\WpService;

/**
 * Class AbstractController
 *
 * @package Modularity\Module\Posts\TemplateController
 */
class AbstractController
{
    /** @var array */
    public $data = [];
    
    /** @var array */
    public $fields = [];

    /** @var \Modularity\Module\Posts\Posts */
    protected $module;

    protected DomainChecker $domainChecker;

    /**
     * AbstractController constructor.
     *
     * @param \Modularity\Module\Posts\Posts $module
    */
    public function __construct(\Modularity\Module\Posts\Posts $module)
    {
        $this->module               = $module;
        $this->fields               = $module->fields;
        $this->domainChecker        = $module->domainChecker;
        $this->data                 = $this->addDataViewData($module->data, $module->fields);
        $this->data['posts']        = $this->preparePosts($module);

        $this->data['classList']    = [];
    }

    /**
     * Get the WpService instance.
     * 
     * @return \WpService\WpService
     */
    private function getWpService(): WpService {
        return WpServiceHelper::get();
    }

    /**
     * Prepare posts for display.
     *
     * @param \Modularity\Module\Posts\Posts $module
     *
     * @return array
    */
    public function preparePosts(\Modularity\Module\Posts\Posts $module)
    {
        $stickyPosts = $module->data['stickyPosts'] ?? [];
        $stickyPosts = $this->addStickyPostsData($stickyPosts);
        $stickyPosts = $this->addPostData($stickyPosts);
        $posts       = $this->addPostData($module->data['posts']);
        $posts       = array_merge($stickyPosts, $posts);

        return $posts;
    }

    /**
     * Prepare and set data fields for posts display.
     *
     * @param array $data
     * @param array $fields
     *
     * @return array
    */
    public function addDataViewData(array $data, array $fields) 
    {
        $data['posts_columns'] = $this->getWpService()->applyFilters('Modularity/Display/replaceGrid', $fields['posts_columns']);
        $data['ratio'] = $fields['ratio'] ?? '16:9';

        $data['highlight_first_column_as'] = $fields['posts_display_highlighted_as'] ?? 'block';
        $data['highlight_first_column'] = !empty($fields['posts_highlight_first']) ? 
            ColumnHelper::getFirstColumnSize($data['posts_columns']) : 
            false;
        $data['imagePosition'] = $fields['image_position'] ?? false;
        $data['showDate'] = in_array('date', $fields['posts_fields'] ?? []);

        return $data;
    }

    /**
     * Prepare posts data by setting default values and post flags.
     *
     * @param \WP_Post[] $posts Array of WP_Post objects
     *
     * @return array
     * TODO: This should require an array, but cant because sometimes it gets null. 
    */
    public function addPostData($posts = [])
    {
        $shouldAddBlogNameToPost = $this->shouldAddBlogNameToPost();

        $posts = array_map(function($post) use ($shouldAddBlogNameToPost) {
            $post->post_content = $this->removePostsModuleBlocksFromContent($post->post_content);
            $data['taxonomiesToDisplay'] = !empty($this->fields['taxonomy_display'] ?? null) ? $this->fields['taxonomy_display'] : [];
            $helperClass = '\Municipio\Helper\Post';
            $helperMethod = 'preparePostObject';
            $helperArchiveMethod = 'preparePostObjectArchive';
            
            if(!class_exists($helperClass) || !method_exists($helperClass, $helperMethod) || !method_exists($helperClass, $helperArchiveMethod)) {
                error_log("Class or method does not exist: {$helperClass}::{$helperMethod} or {$helperClass}::{$helperArchiveMethod}");
                return $post;
            }

            if( $shouldAddBlogNameToPost ) {
                $post = $this->addBlogNameToPost($post);
            }

            if(!empty($post->originalBlogId)) {
                $this->getWpService()->switchToBlog($post->originalBlogId);
            }

            if (isset($this->fields['posts_display_as']) && in_array($this->fields['posts_display_as'], ['expandable-list'])) {
                $post = call_user_func([$helperClass, $helperMethod], $post);
            } else {
                $post = call_user_func([$helperClass, $helperArchiveMethod], $post, $data);
            }

            if(!empty($post->originalBlogId)) {
                $this->getWpService()->restoreCurrentBlog();
            }
            
            $post = clone $post; // Ensure we don't modify the original post object
            return $post;

        }, $posts ?? []);

        if(!empty($posts)) {
            foreach ($posts as $index => &$post) {
                $post               = $this->setPostViewData($post, $index);
                $post->classList    = $post->classList ?? [];
                $post               = $this->addHighlightData($post, $index);

                // Apply $this->getDefaultValuesForPosts() to the post object without turning it into an array
                foreach ($this->getDefaultValuesForPosts() as $key => $value) {
                    if (!isset($post->$key)) {
                        $post->$key = $value;
                    }
                }
            }
        }

        return $posts;
    }

    private function removePostsModuleBlocksFromContent(string $content): string {
        // Use regex to remove Modularity Posts blocks from the content
        $pattern = '/<!--\s*wp:acf\/posts\s.*-->/';
        return preg_replace($pattern, '', $content);
    }

    /**
     * Determine if the blog name should be added to the post.
     *
     * @return bool
     */
    public function shouldAddBlogNameToPost(): bool
    {
        $sources = $this->fields['posts_data_network_sources'] ?? [];
        return is_array($sources) && !empty($sources);
    }

    /**
     * Add blog name to the post object.
     *
     * @param WP_Post $post
     *
     * @return WP_Post
    */
    private function addBlogNameToPost(WP_Post $post): WP_Post {
        static $blogDetailsCache = [];
        $blogId = !empty($post->originalBlogId) ? $post->originalBlogId : $this->getWpService()->getBlogDetails()->blog_id;

        if (!isset($blogDetailsCache[$blogId])) {
            $blogDetailsCache[$blogId] = $this->getWpService()->getBlogDetails($blogId);
        }

        $post->originalSite = $blogDetailsCache[$blogId]->blogname ?? '';

        return $post;
    }

    /**
     * Add post columns class.
     *
     * @param object $post
     * @param false|int $index
     *
     * @return object
    */
    private function addHighlightData(object $post, $index): object
    {
        $columnsClass =  $this->data['posts_columns'] ?? 'o-grid-12@md';

        if (!empty($post->isSticky)) {
            $columnsClass = 'o-grid-12@md';
            $post->isHighlighted = true;
        } elseif ($index === 0 && !empty($this->data['highlight_first_column'])) {
            $columnsClass = $this->data['highlight_first_column'];
            $post->isHighlighted = true;
        }

        $post->classList[] = $columnsClass;

        return $post;
    }

    /**
     * Get default values for keys in the post object.
     *
     * @return array
    */
    private function getDefaultValuesForPosts() {
        return [
            'postTitle' => false,
            'excerptShort' => false,
            'termsUnlinked' => false,
            'postDateFormatted' => false,
            'images' => false,
            'hasPlaceholderImage' => false,
            'readingTime' => false,
            'permalink' => false,
            'id' => false,
            'postType' => false,
            'termIcon' => false,
            'callToActionItems' => false,
            'imagePosition' => true,
            'image' => false,
            'attributeList' => [],
            'isSticky' => false,
            'commentCount' => false,
        ];
    }

    /**
     * Set boolean flags for hiding/showing specific post details.
     *
     * @param object $post
     * @param false|int $index
     *
     * @return object
    */
    private function setPostViewData(object $post, $index = false)
    {
        $post->excerptShort         = in_array('excerpt', $this->data['posts_fields'] ?? []) ? $this->sanitizeExcerpt($this->data['posts_display_as'] === 'news' ? $post->excerpt : $post->excerptShort) : false;
        $post->postTitle            = in_array('title', $this->data['posts_fields'] ?? []) ? $post->getTitle() : false;
        $post->image                = in_array('image', $this->data['posts_fields'] ?? []) ? $post->getImage() : [];
        $post->hasPlaceholderImage  = in_array('image', $this->data['posts_fields'] ?? []) && empty($post->image) ? true : false;
        $post->commentCount         = in_array('comment_count', $this->data['posts_fields'] ?? []) ? (string) $post->getCommentCount() : false;
        $post->readingTime          = in_array('reading_time', $this->data['posts_fields'] ?? []) ? $post->readingTime : false;

        $post->attributeList                    = !empty($post->attributeList) ? $post->attributeList : [];
        $post->attributeList['data-js-item-id'] = $post->getId();

        
        if (
            !empty($this->fields['posts_open_links_in_new_tab']) &&
            !$this->domainChecker->isSameDomain($post->getPermalink())
        ) {
            $post->attributeList['target'] = '_blank';
        }

        if (!empty($post->image) && is_array($post->image)) {
            $post->image['removeCaption'] = true;
            $post->image['backgroundColor'] = 'secondary';
        }

        return $post;
    }

    public function postUsesSchemaTypeEvent(object $post):bool {
        return $post->getSchemaProperty('@type') === 'Event';
    }

    /**
     * Sanitize excerpt by stripping tags, normalizing whitespace, trimming, and converting newlines to <br>.
     *
     * @param string $excerpt
     *
     * @return string
    */
    private function sanitizeExcerpt(string $excerpt)
    {
        $excerpt = strip_tags($excerpt);
        $excerpt = preg_replace("/[\r\n]+/", "\n", $excerpt);
        $excerpt = trim($excerpt);
        $excerpt = nl2br($excerpt);

        return $excerpt;
    }

    /**
     * Add sticky posts data.
     *
     * @param array $stickyPosts
     *
     * @return array
    */
    private function addStickyPostsData(array $stickyPosts = [])
    {
        if (empty($stickyPosts)) {
            return [];
        }

        foreach ($stickyPosts as &$post) {
            $post->isSticky    = true;
        }

        return $stickyPosts;
    }
}
