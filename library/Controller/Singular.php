<?php

namespace Municipio\Controller;

use Municipio\Helper\Archive;

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
        $originalPostData = get_post($this->getPageID());

        $this->data['post'] = \Municipio\Helper\Post::preparePostObject($originalPostData);
        $this->data['isBlogStyle'] = in_array($this->data['post']->postType, ['post', 'nyheter']) ? true : false;

        //Get feature image data
        $this->data['featuredImage'] = $this->getFeaturedImage($this->data['post']->id, [4096, false]);

        //Signature options
        $this->data['signature'] = $this->getSignature();

        $this->data['publishTranslations'] = (object) array(
            'updated'   => __('Updated', 'municipio'),
            'publish'   => __('Published', 'municipio'),
            'by'        => __('Published by', 'municipio'),
            'on'        => __('on', 'municipio'),
        );

        //Reading time
        $this->data['readingTime']          = $this->getReadingTime($this->data['post']->postContent);

        //Comments
        if (get_option('comment_moderation') === '1') {
            $this->data['comments'] = get_approved_comments($this->data['post']->id, array(
                'order'     => get_option('comment_order')
            ));
        } else {
            $this->data['comments'] = get_comments(array(
                'post_id'   => $this->data['post']->id,
                'order'     => get_option('comment_order')
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
        $this->data['settingItems'] = apply_filters_deprecated('Municipio/blog/post_settings', array($this->data['post']), '3.0', 'Municipio/blog/postSettings');

        //Should link author page
        $this->data['authorPages'] = apply_filters('Municipio/author/hasAuthorPage', false);

        //Main content padder
        $this->data['mainContentPadding'] = $this->getMainContentPadding($this->data['customizer']);

        $this->data['postAgeNotice'] = $this->getPostAgeNotice($this->data['post']);

        if (!isset($this->data['lang'])) {
            $this->data['lang'] = (object) [];
        }

        $this->data['placeQuicklinksAfterContent'] = $this->displayQuicklinksAfterContent($this->data['post']->id);

        //Secondary Query

        $this->data = $this->setupSecondaryQueryData($this->data);

        $this->data['lang']->readingTime = __('Reading time', 'municipio');

        return $this->data;
    }

    protected function setupSecondaryQueryData($data)
    {
        $data['secondaryQuery'] = $this->prepareQuery(get_query_var('secondaryQuery'));
        if ('' === $data['secondaryQuery'] || !$data['secondaryQuery']) {
            $data['secondaryQuery']        = false;
            $data['secondaryPostType']     = false;
            $data['displaySecondaryQuery'] = false;
            return $data;
        }

        $currentPath       = (string) parse_url(home_url() . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $secondaryPostType = $data['secondaryQuery']->query['post_type'];

        $data['secondaryPostType']       = $secondaryPostType;
        $data['secondaryArchiveProps']   = Archive::getArchiveProperties(
            $secondaryPostType,
            $data['customizer']
        );
        $secondaryArchiveProps = $data['secondaryArchiveProps'];

        $data['secondaryTemplate']       = Archive::getTemplate($secondaryArchiveProps);
        $data['secondaryPaginationList'] = Archive::getPagination(
            '',
            $data['secondaryQuery']
        );
        $data['showSecondaryPagination'] = Archive::showPagination(
            $currentPath,
            $data['secondaryQuery']
        );

        $data['currentPage']          = get_query_var('paged') ?? 1;
        $data['gridColumnClass']      = Archive::getGridClass($secondaryArchiveProps);
        $data['displayReadingTime']   = Archive::displayReadingTime($secondaryArchiveProps);
        $data['displayFeaturedImage'] = Archive::displayFeaturedImage($secondaryArchiveProps);
        $data['showFilter']           = Archive::showFilter($secondaryArchiveProps);
        $data['facettingType']        = Archive::getFacettingType($secondaryArchiveProps);
        $data['selectedFilters'] = \apply_filters('Municipio/secondaryQuery/selectedFilters', (array) $_GET);
        $data['enabledFilters'] = $this->getSecondaryTaxonomyFilters($secondaryArchiveProps, $data);

        $data['lang']->filterBtn        = __('Filter', 'municipio');
        $data['lang']->resetFilterBtn   = __('Reset filter', 'municipio');
        $data['lang']->noResult         = __('No items found.', 'municipio');

        $data['archiveResetUrl'] = get_permalink(add_query_arg(array(), ''));
        $data['showFilterReset'] = Archive::showFilterReset($data['selectedFilters']);

        $data['displaySecondaryQuery'] = apply_filters('Municipio/Controller/Singular/displaySecondaryQuery', !empty($data['secondaryQuery']->posts));

        return $data;
    }
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
     * Retrieve an array of taxonomy objects based on the given arguments.
     *
     * @param object $args The arguments for the function.
     * @param array $data An optional array of data.
     * @return array An array of taxonomy objects.
     */
    protected function getSecondaryTaxonomyFilters($args, array $data = [])
    {
        if (empty($args->enabledFilters)) {
            return \apply_filters('Municipio/secondaryQuery/getSecondaryTaxonomyFilters', [], $data);
        }

        $taxonomies = apply_filters('Municipio/secondaryQuery/enabledFilters', $args->enabledFilters, $data);
        $taxonomyObjects = [];

        foreach ($taxonomies as $tax) {
            $taxonomy = get_taxonomy($tax);

            if ($taxonomy) {
                $terms = get_terms(
                    apply_filters(
                        'Municipio/secondaryQuery/getTermsArgs',
                        ['taxonomy' => $taxonomy->name],
                        $data
                    )
                );

                if (!empty($terms) && !is_wp_error($terms)) {
                    $options = [];

                    foreach ($terms as $term) {
                        $options[$term->slug] = htmlspecialchars_decode(ucfirst($term->name));
                    }

                    $taxonomyObjects[] = [
                        'label' => __("Select", 'municipio') . " " . strtolower($taxonomy->labels->singular_name),
                        'attributeList' => [
                           'name' => "{$taxonomy->name}[]"
                        ],
                        'options' => $options,
                        'preselected' => $data['selectedFilters'][$taxonomy->name] ?? false,
                    ];
                }
            }
        }

        return \apply_filters('Municipio/secondaryQuery/getSecondaryTaxonomyFilters', $taxonomyObjects, $data);
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
        $postId         = $this->data['post']->id;
        $displayAuthor  = get_field('page_show_author', 'option');
        $displayAvatar  = get_field('page_show_author_image', 'option');
        $linkAuthor     = get_field('page_link_to_author_archive', 'option');

        $displayPublish = in_array($this->data['postType'], (array) get_field('show_date_published', 'option'));
        $displayUpdated = in_array($this->data['postType'], (array) get_field('show_date_updated', 'option'));

        if ($displayPublish) {
            $published  = $this->getPostDates($this->data['post']->id)->published;
        }

        if ($displayUpdated) {
            $updated    = $this->getPostDates($this->data['post']->id)->updated;
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
        'id' => $this->data['post']->postAuthor,
        'link' => get_author_posts_url($this->data['post']->postAuthor),
        'name' => null,
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
        'updated' => get_the_modified_date()
        ]);
    }

    /**
     * @param $postId Post id
     * @param $size Name or array for size of image
     * @return array An array of data related to the image
     */
    private function getFeaturedImage($postId, $size = [1920,1080])
    {
        //Check option if it should be displayed
        if (get_field('post_single_show_featured_image', $postId) == false) {
            return false;
        }

        //Get the image id
        $featuredImageId = get_post_thumbnail_id($postId);

        //Bail out if not found
        if (!is_numeric($featuredImageId)) {
            return false;
        }

        $featuredImageObject = (object) [
        'id'    => $featuredImageId,
        'src'   => wp_get_attachment_image_src($featuredImageId, apply_filters('Municipio/Controller/Singular/featuredImageSize', $size)),
        'alt'   => get_post_meta($featuredImageId, '_wp_attachment_image_alt', true),
        'title' => get_the_title($featuredImageId)
        ];

        return apply_filters('Municipio/Controller/Singular/featureImage', $featuredImageObject);
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
        if (! is_object($post)) {
            return false;
        }

        if (function_exists('get_field')) {
            $postTypes = (array) get_field('avabile_dynamic_post_types', 'option');

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

    public function displayQuicklinksAfterContent($postId)
    {
        $displayAfterContent = (bool) get_field('quicklinks_after_content', $postId);
        return apply_filters('Municipio/Controller/Singular/displayQuicklinksAfterContent', $displayAfterContent, $postId);
    }
}
