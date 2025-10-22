<?php

namespace Municipio\Controller;

use Municipio\Helper\WP;
use Municipio\PostObject\PostObjectInterface;
use WP_Post;

/**
 * Class Singular
 * @package Municipio\Controller
 */
class Singular extends \Municipio\Controller\BaseController
{
    protected PostObjectInterface $post;

    /**
     * @return array|void
     */
    public function init()
    {
        parent::init();

        //Get post data
        $pageID           = $this->getPageID();
        $originalPostData = $this->getOriginalPostData($pageID);

        if (!$originalPostData) {
            return $this->data;
        }

        $originalPostData = $this->displayQuicklinksAfterFirstBlock($originalPostData);

        $this->post                = \Municipio\Helper\Post::preparePostObject($originalPostData, $this->data);
        $this->data['post']        = $this->post;
        $this->data['isBlogStyle'] = in_array($this->data['post']->postType, ['post', 'nyheter']) ? true : false;

        $this->data['displayFeaturedImage']        = $this->maybeRunInOtherSite(fn() => $this->displayFeaturedImageOnSinglePost($this->post->getId()));
        $this->data['displayFeaturedImageCaption'] = $this->maybeRunInOtherSite(fn() => $this->displayFeaturedImageCaptionOnSinglePost($this->post->getId()));
        $this->data['showPageTitleOnOnePage']      = $this->maybeRunInOtherSite(fn() => $this->showPageTitleOnOnePage($this->post->getId()));
        $this->data['featuredImage']               = $this->maybeRunInOtherSite(fn () => $this->getFeaturedImage($this->post->getId(), [1366, 910]));

        //Signature options
        $this->data['signature'] = $this->getSignature(
            $this->data['post']
        );

        //Reading time
        $this->data['readingTime'] = $this->getReadingTime($this->data['post']->postContent);

        //Comments
        if (get_option('comment_moderation') === '1') {
            $this->data['comments'] = get_approved_comments($this->data['post']->id, array(
                'order' => get_option('comment_order')
            ));
        } else {
            $this->data['comments'] = get_comments(array(
                'post_id' => $this->data['post']->id,
                'order'   => get_option('comment_order')
            ));
        }

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
        $this->data['settingItems'] = apply_filters_deprecated(
            'Municipio/blog/post_settings',
            array($this->data['post']),
            '3.0',
            'Municipio/blog/postSettings'
        );

        //Should link author page
        $this->data['authorPages'] = apply_filters('Municipio/author/hasAuthorPage', false);

        //Main content padder
        $this->data['mainContentPadding'] = $this->getMainContentPadding($this->data['customizer']);

        //Get age of post
        $this->data['postAgeNotice'] = $this->getPostAgeNotice($this->data['post']);

        return $this->data;
    }

    /**
     * Run a callable in the context of the post's site.
     *
     * @param callable $callable The callable to run.
     *
     * @return mixed The result of the callable.
     */
    public function maybeRunInOtherSite(callable $callable): mixed
    {
        return $this->siteSwitcher->runInSite($this->post->getBlogId(), $callable);
    }

    /**
     * Prepare the query object by enhancing each post within the query result.
     *
     * @param WP_Query $query The query object to prepare.
     *
     * @return WP_Query|bool The prepared query object, or false if the input is not a valid query.
     */
    public function prepareQuery($query)
    {
        if (is_string($query) || empty($query)) {
            return false;
        }
        if ($query->have_posts()) {
            foreach ($query->posts as &$post) {
                $post = \Municipio\Helper\Post::preparePostObject($post);
            }
        }

        return $query;
    }

    /**
     * Get main content padder size
     */
    public function getMainContentPadding($customizer): array
    {
        //Name shorten
        $padding = $customizer->mainContentPadding;

        //Validate, and send var to view.
        if (!empty($padding) && is_numeric($padding) && ($padding % 2 == 0)) {
            //Make md span half the size of padding
            return [
            'md' => ($padding / 2),
            'lg' => $padding
            ];
        }

        //Return default values
        return [
        'md' => 0,
        'lg' => 0
        ];
    }

    /**
     * Inserts a "Quicklinks" block after the first block in the post content if certain conditions are met.
     *
     * @param WP_Post|null $postObject The post object whose content will be modified. Can be null.
     *
     * @return WP_Post|null
     */
    private function displayQuicklinksAfterFirstBlock(?WP_Post $postObject): ?WP_Post
    {
        if (
            !$postObject ||
            $this->data['quicklinksPlacement'] !== 'after_first_block' ||
            !$this->wpService->hasBlocks($postObject->post_content) ||
            empty($this->data['quicklinksMenu']['items'])
        ) {
            return $postObject;
        }

        $blocks = $this->wpService->parseBlocks($postObject->post_content);

        $html = render_blade_view(
            'partials.navigation.fixed-after-block',
            [
                'quicklinksMenu'      => $this->data['quicklinksMenu'],
                'quicklinksPlacement' => $this->data['quicklinksPlacement'],
                'customizer'          => $this->data['customizer'],
                'lang'                => $this->data['lang'],
                'isFrontPage'         => $this->data['isFrontPage'],
            ]
        );

        $quicklinksBlock = [
            'blockName'    => 'core/html',
            'attrs'        => ["name" => "quicklinks"],
            'innerHTML'    => $html,
            'innerContent' => [$html],
            'innerBlocks'  => [],
        ];

        array_splice($blocks, 1, 0, [$quicklinksBlock]);
        $postObject->post_content = $this->wpService->serializeBlocks($blocks);

        return $postObject;
    }

    /**
     * @return mixed
     */
    public function getSignature(PostObjectInterface $post): object
    {
        $displayAuthor = $this->acfService->getField('page_show_author', 'option');
        $displayAvatar = $this->acfService->getField('page_show_author_image', 'option');
        $linkAuthor    = $this->acfService->getField('page_link_to_author_archive', 'option');

        $displayPublish = in_array($this->data['postType'], (array) $this->acfService->getField('show_date_published', 'option') ?? []);
        $displayUpdated = in_array($this->data['postType'], (array) $this->acfService->getField('show_date_updated', 'option') ?? []);

        return $this->maybeRunInOtherSite(function () use ($post, $displayAuthor, $displayAvatar, $linkAuthor, $displayPublish, $displayUpdated) {
            return (object) [
                'avatar'    => ($displayAvatar ? $this->getAuthor($post->getId())->avatar : ""),
                'role'      => ($displayAuthor ? __("Author", 'municipio') : ""),
                'name'      => ($displayAuthor ? $this->getAuthor($post->getId())->name : ""),
                'link'      => ($linkAuthor ? $this->getAuthor($post->getId())->link : ""),
                'published' => ($displayPublish ? $post->getPublishedTime() : false),
                'updated'   => ($displayUpdated ? $post->getModifiedTime() : false),
            ];
        }, []);
    }

    /**
     * @param $id
     * @return object
     */
    private function getAuthor($id): object
    {
        $author = array(
        'id'     => $this->data['post']->postAuthor,
        'link'   => get_author_posts_url($this->data['post']->postAuthor),
        'name'   => null,
        'avatar' => null
        );

        //Get setting for username
        $displayName = get_the_author_meta('display_name', $this->data['post']->postAuthor);

        //List of less-fancy displaynames
        $prohoboitedUserNames = [
        get_the_author_meta('user_login', $this->data['post']->postAuthor),
        get_the_author_meta('nickname', $this->data['post']->postAuthor)
        ];

        //Assign only if fancy variant of name
        if (!in_array($displayName, $prohoboitedUserNames)) {
            $author['name'] = $displayName;
        }

        //Get avatar url
        $avatar = get_avatar_url($id, ['default' => 'blank']);
        if (!preg_match('/d=blank/i', $avatar)) {
            $author['avatar'] = $avatar;
        }

        return apply_filters('Municipio/Controller/Singular/author', (object) $author);
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getPostDates($id): object
    {
        return apply_filters('Municipio/Controller/Singular/publishDate', (object) [
        'published' => get_the_date(),
        'updated'   => get_the_modified_date()
        ]);
    }

    /**
     * @param $postId Post id
     * @param $size Name or array for size of image
     * @return array An array of data related to the image
     */
    private function getFeaturedImage($postId, $size = [1920,false])
    {
        //Check option if it should be displayed
        if (get_field('post_single_show_featured_image', $postId) == false) {
            return false;
        }

        $featuredImage = \Municipio\Helper\Post::getFeaturedImage($postId, $size);

        return apply_filters('Municipio/Controller/Singular/featureImage', $featuredImage);
    }

    /**
     * Calculate reading time
     *
     * @param   string      $postContent    The post content
     * @param   integer     $factor         What factor to devide with, default 200 = normal reading speed
     * @return  integer                     Interger representing number of reading minutes
     */
    public function getReadingTime($postContent, $factor = 200)
    {
        return \Municipio\Helper\ReadingTime::getReadingTime($postContent, $factor);
    }

    /**
     * > This function takes a date string and returns the number of days since that date
     *
     * @param string postDate The date the post was created.
     *
     * @return The difference in days between the current date and the date the post was created.
     */
    public function getPostAge(string $postDate)
    {
        if (! $postDate) {
            return;
        }

        $created = date_create($postDate);
        $now     = date_create();
        $diff    = date_diff($created, $now);

        return $diff->days;
    }

    /**
     * If the post type is set to display age notification, and the post is older than the set number
     * of days, return a string with the number of days
     *
     * @param object post The post object
     *
     * @return A string
     */
    public function getPostAgeNotice(object $post)
    {
        if (!is_object($post)) {
            return false;
        }

        if (function_exists('get_field')) {
            $postTypes = (array) get_field('avabile_dynamic_post_types', 'option');
            $postTypes = array_filter($postTypes);

            if (is_array($postTypes) && !empty($postTypes)) {
                foreach ($postTypes as $type) {
                    $thisType = get_post_type_object($post->postType)->rewrite['slug'] ?? '';
                    if (isset($type['slug']) && $type['slug'] !== $thisType) {
                        continue;
                    }

                    $type = (object) \Municipio\Helper\FormatObject::camelCase($type);
                    if (
                        isset($type->displayAgeNotificationOnPosts)
                        && $type->displayAgeNotificationOnPosts === (bool) true
                    ) {
                        $postAge = $this->getPostAge($post->postDate);
                        if ($postAge > $type->postAgeDays) {
                            return sprintf(
                                _n(
                                    'This content was published more than %s day ago.',
                                    'This content was published more than %s days ago.',
                                    $type->postAgeDays,
                                    'municipio'
                                ),
                                $type->postAgeDays
                            );
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Determine whether to display the featured image on a single post based on configuration.
     *
     * @param int $postId The ID of the post (optional, default is 0 for the current post).
     *
     * @return bool True if the featured image caption should be displayed, false otherwise.
     */
    private function displayFeaturedImageCaptionOnSinglePost(int $postId)
    {
        return (bool) apply_filters(
            'Municipio/Controller/Singular/displayFeaturedImageCaptionOnSinglePost',
            get_field('post_single_show_featured_image_caption', $postId),
            $postId
        );
    }

    /**
     * Determine whether to display the featured image on a single post based on configuration.
     *
     * @param int $postId The ID of the post (optional, default is 0 for the current post).
     *
     * @return bool True if the featured image should be displayed, false otherwise.
     */
    private function displayFeaturedImageOnSinglePost(int $postId)
    {
        return (bool) apply_filters(
            'Municipio/Controller/Singular/displayFeaturedImageOnSinglePost',
            get_field('post_single_show_featured_image', $postId),
            $postId
        );
    }

    /**
     * Determine whether to show the page title on a one-page post based on configuration.
     *
     * @param int $postId The ID of the post (optional, default is 0 for the current post).
     *
     * @return bool True if the page title should be shown on a one-page post, false otherwise.
     */
    private function showPageTitleOnOnePage(int $postId)
    {
        return (bool) apply_filters(
            'Municipio/Controller/Singular/showTitleOnOnePage',
            get_field('post_one_page_show_title', $postId),
            $postId
        );
    }

    /**
     * Get the original post data
     *
     * @param int $pageID The page id
     * @return WP_Post|null
     */
    protected function getOriginalPostData(int $pageID): ?WP_Post
    {
        if (isset($GLOBALS['post']) && is_a($GLOBALS['post'], 'WP_Post') && $GLOBALS['post']->ID === $pageID) {
            $post = $GLOBALS['post'];
        } else {
            $post = WP::getPost($pageID);
        }

        return $post instanceof WP_Post ? $post : null;
    }

    /**
     * Get related posts based on the taxonomies of the current post.
     *
     * @param int $postId The ID of the current post.
     *
     * @return array|bool An array of related posts or false if no related posts are found.
     */
    public function getRelatedPosts(int $postId)
    {
        // Get the taxonomies associated with the post
        $taxonomies = get_post_taxonomies($postId);

        $taxQuery = [];
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($postId, $taxonomy);
            if (!empty($terms)) {
                $termIds    = wp_list_pluck($terms, 'term_id');
                $taxQuery[] = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $termIds,
                    'operator' => 'IN',
                ];
            }
        }

        // If no terms were found, return false
        if (empty($taxQuery)) {
            return false;
        }

        // Get the current post type
        $postType = get_post_type($postId);

        // Define the query arguments
        $args = [
            'numberposts'  => 3,
            'post_type'    => $postType,
            'post__not_in' => [$postId],
            'tax_query'    => $taxQuery,
            'orderby'      => 'rand'
        ];

        // Get the related posts
        $relatedPosts = get_posts($args);

        // If posts were found, prepare them
        if (!empty($relatedPosts)) {
            foreach ($relatedPosts as &$post) {
                $post = \Municipio\Helper\Post::preparePostObject($post);
            }
            return $relatedPosts;
        }

        // Return false if no related posts were found
        return false;
    }
}
