<?php

namespace Municipio\Controller;

use Municipio\Helper\Navigation;
use Municipio\Helper\Archive;
use Municipio\Helper\WP;
use WP_Post;

/**
 * Class Singular
 * @package Municipio\Controller
 */
class Singular extends \Municipio\Controller\BaseController
{
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

        $this->data['post']        = \Municipio\Helper\Post::preparePostObject($originalPostData, $this->data);
        $this->data['isBlogStyle'] = in_array($this->data['post']->postType, ['post', 'nyheter']) ? true : false;

        $this->data['displayFeaturedImage']   = $this->displayFeaturedImageOnSinglePost($this->data['post']->id);
        $this->data['showPageTitleOnOnePage'] = $this->showPageTitleOnOnePage($this->data['post']->id);

        $this->data['quicklinksPlacement']           = $this->data['post']->quicklinksPlacement;
        $this->data['displayQuicklinksAfterContent'] = $this->data['post']->displayQuicklinksAfterContent;
        $this->data['featuredImage']                 = $this->getFeaturedImage($this->data['post']->id, [1366, 910]);

        //Signature options
        $this->data['signature'] = $this->getSignature();

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
        $this->data['postAgeNotice']               = $this->getPostAgeNotice($this->data['post']);

        //Position of quicklinks
        $this->data['placeQuicklinksAfterContent'] = Navigation::displayQuicklinksAfterContent($this->data['post']->id);

        return $this->data;
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
     * @return mixed
     */
    public function getSignature(): object
    {
        $postId        = $this->data['post']->id;
        $displayAuthor = get_field('page_show_author', 'option');
        $displayAvatar = get_field('page_show_author_image', 'option');
        $linkAuthor    = get_field('page_link_to_author_archive', 'option');

        $displayPublish = in_array($this->data['postType'], (array) get_field('show_date_published', 'option'));
        $displayUpdated = in_array($this->data['postType'], (array) get_field('show_date_updated', 'option'));

        if ($displayPublish) {
            $published = $this->getPostDates($this->data['post']->id)->published;
        }

        if ($displayUpdated) {
            $updated = $this->getPostDates($this->data['post']->id)->updated;
        }

        return (object) [
        'avatar'    => ($displayAvatar ? $this->getAuthor($postId)->avatar : ""),
        'role'      => ($displayAuthor ? __("Author", 'municipio') : ""),
        'name'      => ($displayAuthor ? $this->getAuthor($postId)->name : ""),
        'link'      => ($linkAuthor ? $this->getAuthor($postId)->link : ""),
        'published' => ($displayPublish ? $published : false),
        'updated'   => ($displayUpdated ? $updated : false),
        ];
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
        $taxonomies = get_post_taxonomies($postId);
        $postTypes  = get_post_types(
            [
                'public'   => true,
                '_builtin' => false
            ],
            'objects'
        );

        $arr = [];
        foreach ($taxonomies as $taxonomy) {
            $terms = get_the_terms($postId, $taxonomy);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if ($term instanceof \WP_Term) {
                        $arr[$taxonomy][] = $term->term_id;
                    }
                }
            }
        }

        if (empty($arr)) {
            return false;
        }

        $posts = [];
        foreach ($postTypes as $postType) {
            $args = [
                'numberposts'  => 3,
                'post_type'    => $postType->name,
                'post__not_in' => [$postId],
                'tax_query'    => [
                    'relation' => 'OR',
                ],
            ];

            foreach ($arr as $tax => $ids) {
                $args['tax_query'][] = [
                'taxonomy' => $tax,
                'field'    => 'term_id',
                'terms'    => $ids,
                'operator' => 'IN',
                ];
            }

            $result = get_posts($args);

            if (!empty($result)) {
                foreach ($result as &$post) {
                    $post                    = \Municipio\Helper\Post::preparePostObject($post);
                    $posts[$postType->label] = $result;
                }
            }
        }

        return $posts;
    }
}
