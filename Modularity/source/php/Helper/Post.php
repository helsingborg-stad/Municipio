<?php

namespace Modularity\Helper;

class Post
{
    /**
     * Gets the post template of the current editor page
     * @return string Template slug
     */
    public static function getPostTemplate($id = null, $trim = false)
    {
        if ($archive = self::isArchive()) {
            return $archive;
        }

        global $post;

        // If $post is empty try to fetc post from querystring
        if (!$post && isset($_GET['id']) && is_numeric($_GET['id'])) {
            $post = get_post($_GET['id']);

            if (!$post) {
                throw new \Error('The requested post was not found.');
            }
        }

        if (!$post) {
            return isset($_GET['id']) && !empty($_GET['id']) ? $_GET['id'] : $archive;
        }

        // If post is set, fetch the template
        $template = get_page_template_slug($post->ID);

        // If this is the front page and the template is set to page.php or page.blade.php default to just "page"
        if ($post->ID === (int)get_option('page_on_front') && in_array($template, array('page.php', 'page.blade.php'))) {
            return 'page';
        }

        if (!$template) {
            $template = self::detectCoreTemplate($post);
        }

        $template = $trim ? str_replace('.blade.php', '', $template) : $template;

        return $template;
    }

    /**
     * Detects core templates
     * @return string Template
     */
    public static function detectCoreTemplate($post)
    {
        if ((int)get_option('page_on_front') == (int)$post->ID) {
            return \Modularity\Helper\Wp::findCoreTemplates(array(
                'front-page',
                'page'
            ));
        }

        switch ($post->post_type) {
            case 'post':
                return 'single';
                break;

            case 'page':
                return 'page';
                break;

            default:
                return \Modularity\Helper\Wp::findCoreTemplates(array(
                    'single-' . $post->post_type,
                    'single',
                    'page'
                ));
                break;
        }

        return 'index';
    }

    /**
     * Verifies if the current page is an archive or search result page
     * @return boolean
     */
    public static function isArchive()
    {
        global $archive;
        global $post;

        $archive = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';

        if (is_string($archive) && substr($archive, 0, 8) == 'archive-' || is_search()) {
            return $archive;
        }

        if (is_string($archive) && substr($archive, 0, 7) == 'single-' || is_search()) {
            return $archive;
        }

        if (is_archive() && (is_object($post) && $post->post_type == 'post')) {
            return 'archive';
        } elseif (is_search() || (is_object($post) && is_post_type_archive($post->post_type))) {
            return 'archive-' . $post->post_type;
        } elseif ($archive == 'author') {
            return 'author';
        }

        return false;
    }

    /**
     * Get the archive id
     * @return string|bool
     */
    public static function getArchiveId(): string|bool
    {
        if($archive = self::isArchive()) {
            return $archive; 
        }
        return false;
    }

    /**
     * Remove empty ptags from string
     *
     * @param string $string    A string that may contain empty ptags
     * @return string           A string that not contain empty ptags
     */
    private static function removeEmptyPTag($string) {
        return preg_replace("/<p[^>]*>(?:\s|&nbsp;)*<\/p>/", '', $string);
    }

    /**
     * Get the post featured image
     *
     * @param integer   $postId         
     * @return array    $featuredImage  The post thumbnail image, with alt and title
     */
    public static function getFeaturedImage($postId, $size = 'full')
    {
        $featuredImageID = get_post_thumbnail_id($postId);

        $featuredImageSRC = \get_the_post_thumbnail_url(
            $postId,
            apply_filters('Modularity/Helper/Post/FeaturedImageSize', $size)
        );
        $featuredImageAlt   = get_post_meta($featuredImageID, '_wp_attachment_image_alt', true);
        $featuredImageTitle = get_the_title($featuredImageID);

        $featuredImage = [
            'src' => $featuredImageSRC ? $featuredImageSRC : null,
            'alt' => $featuredImageAlt ? $featuredImageAlt : null,
            'title' => $featuredImageTitle ? $featuredImageTitle : null
        ];

        return \apply_filters('Modularity/Helper/Post/FeaturedImage', $featuredImage);
    }

    /**
     * Get a list of terms to display on each inlay
     *
     * @param integer $postId           The post identifier
     * @param boolean $includeLink      If a link should be included or not
     * @return array                    A array of terms to display
     */
    protected static function getPostTerms($postId, $includeLink = false)
    {
        $taxonomies = get_field('archive_' . get_post_type($postId) . '_post_taxonomy_display', 'options');

        $termsList = [];

        if (is_array($taxonomies) && !empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_post_terms($postId, $taxonomy);

                if (!empty($terms)) {
                    foreach ($terms as $term) {

                        $item = [];

                        $item['label'] = $term->name ?? '';

                        if ($includeLink) {
                            $item['href'] = get_term_link($term->term_id);
                        }

                        $termsList[] = $item;
                    }
                }
            }
        }

        return \apply_filters('Modularity/Helper/Post/getPostTerms', $termsList, $postId);
    }

    /**
     * Get current page ID
     */
    public static function getPageID(): int
    {
        //Page for posttype archive mapping result
        if (is_post_type_archive()) {
            if ($pageId = get_option('page_for_' . get_post_type())) {
                return $pageId;
            }
        }

        //Get the queried page
        if (get_queried_object_id()) {
            return get_queried_object_id();
        }

        //Return page for frontpage (fallback)
        if ($frontPageId = get_option('page_on_front')) {
            return $frontPageId;
        }

        //Return page blog (fallback)
        if ($frontPageId = get_option('page_for_posts')) {
            return $frontPageId;
        }

        return 0;
    }
}
