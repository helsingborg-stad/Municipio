<?php

namespace Municipio\Theme;

class General
{
    public function __construct()
    {
        add_action('init', array($this, 'bemItClassDefinition'));

        add_filter('body_class', array($this, 'appendBEMITCssClass'));
        add_filter('body_class', array($this, 'isChildTheme'));
        add_filter('body_class', array($this, 'e404classes'));

        add_filter('private_title_format', array($this, 'titleFormat'));
        add_filter('protected_title_format', array($this, 'titleFormat'));

        add_filter('accessibility_items', array($this, 'accessibilityItems'), 10, 1);
        
        add_filter('the_lead', array($this, 'theLead'));
        add_filter('the_content', array($this, 'removeEmptyPTag'));
        
        add_filter('the_content', array($this, 'normalizeImages'), 99, 1);

        add_filter('img_caption_shortcode_width', array($this, 'normalizeImageCaptionSize'));
        add_filter('img_caption_shortcode_height', array($this, 'normalizeImageCaptionSize'));
        add_filter('acf/get_field_group', array($this, 'fixFieldgroupLocationPath'));

        add_filter('Modularity\Module\Sites\image_rendered', array($this, 'sitesGridImage'), 10, 2);
        add_filter('Modularity\ModularityIconsLibrary', function () {
            return MUNICIPIO_PATH . "assets/dist/data/ico.json";
        }, 10, 0);
        
        remove_filter('template_redirect', 'redirect_canonical');

        //Menu cache purging
        add_action('updated_post_meta', array($this, 'purgeMenuCache'), 10, 4);

        add_filter('Municipio/bodyClass', function ($class) {
            $class .= get_theme_mod('hamburger_menu_enabled') && get_theme_mod('hamburger_menu_mobile') ? ' hamburger-menu-mobile' : '';
            $class .= get_theme_mod('header_sticky') === 'sticky' ? ' sticky-header' : '';
            return $class;
        });

        add_filter('Municipio/HeaderHTML', function ($html) {
            return str_replace(
                ' />',
                '>',
                $html
            );
        });
    }

    /**
     * Purge cache of menu on post meta key save
     *
     * TODO: Find a better place for this.
     *
     * @return bool
     */
    public function purgeMenuCache($metaId, $objectId, $metaKey, $metaValue)
    {
        $bannableKeys = wp_cache_get('municipioNavMenu');
        
        if (is_array($bannableKeys) && in_array($metaKey, $bannableKeys)) {
            return wp_cache_delete($metaKey);
        }
        return false;
    }

    /**
     * Wordpress adds 10 px to captionized images.
     * This resets that.
     *
     * @param integer $size
     * @return integer $size - 10
     */
    public function normalizeImageCaptionSize($size)
    {
        return false;
    }

    /**
     * Defines global BEM class for theme
     *
     * @return void
     */
    public function bemItClassDefinition()
    {
        //Classes
        $classes = array();

        //Theme specific class
        $themeObject = wp_get_theme();
        $classes[] = "t-" . sanitize_title($themeObject->get("Name"));

        //Child theme specific class
        if (is_child_theme()) {
            $childThemeObject = wp_get_theme(get_template());
            $classes[] = "t-" . sanitize_title($childThemeObject->get("Name"));
        }

        //Define const for later use
        define("MUNICIPIO_BEM_THEME_NAME", implode(" ", $classes));
    }

    /**
     * Adds a error 404 class to body element
     *
     * @param array $classes Array contining previously added classes
     *
     * @return void
     */
    public function e404classes($classes)
    {
        if (is_404()) {
            $classes[] = 'error404';
        }

        return $classes;
    }

    /**
     * Returns image for module site grid
     *
     * @param string $image String containing previous image
     * @param object $site  The site object
     *
     * @return string
     */
    public function sitesGridImage($image, $site)
    {
        switch_to_blog($site->blog_id);

        $image = null;

        if ($frontpage = get_option('page_on_front') && get_the_post_thumbnail_url(get_option('page_on_front'))) {
            $src = get_the_post_thumbnail_url($frontpage);

            if ($src) {
                $image = '<div style="background-image:url(' . $src . ');" class="box-image">
                   <img alt="' . $site->blogname . '" src="' . $src . '">
                </div>';
            }
        }

        if (!$image && $logo = get_field('logotype_negative', 'option')) {
            $image = '<div class="box-image">
               ' . \Municipio\Helper\Svg::extract($logo['url']) . '
            </div>';
        }

        restore_current_blog();

        return $image;
    }

    /**
     * Fixes fieldgroups page-template path
     *
     * @param array $fieldgroup Fieldgroup
     *
     * @return array
     */
    public function fixFieldgroupLocationPath($fieldgroup)
    {
        if (!isset($fieldgroup['location'])) {
            return $fieldgroup;
        }

        foreach ($fieldgroup['location'] as &$locations) {
            foreach ($locations as &$location) {
                if ($location['param'] !== 'page_template') {
                    return $fieldgroup;
                }

                $location['value'] = basename($location['value']);
            }
        }

        return $fieldgroup;
    }

    /**
     * Reset title format.
     *
     * @param string $format The previously defined format (discarded)
     *
     * @return string
     */
    public function titleFormat($format)
    {
        return '%s';
    }

    /**
     * Creates a lead paragraph
     *
     * @param string $text Text
     *
     * @return string       Markup
     */
    public function theLead($text)
    {
        return '<p class="lead">' . strip_shortcodes($text) . '</p>';
    }

    /**
     * Removes empty p-tags
     *
     * @param string $content Text
     *
     * @return string       Markup
     */
    public function removeEmptyPTag($content)
    {
        $content    = force_balance_tags($content);
        $content    = preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
        $content    = preg_replace('~\s?<p>(\s|&nbsp;)+</p>\s?~', '', $content);

        return $content;
    }
    /**
     * It takes a string of HTML, finds all images and links containing images, and replaces them with
     * a blade template version of themselves.
     *
     * If an image is linked to itself, it will be replaced with a template version with the attribute `opendModal` set to `true`.
     *
     * @param content The content to be parsed.
     *
     * @return The content of the post.
     */
    public function normalizeImages($content)
    {
        if (str_contains($content, '<img')) {
            $dom = new \DOMDocument;
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
                   
            $images = $dom->getElementsByTagName('img');
            $links = $dom->getElementsByTagName('a');
            
            foreach ($links as $link) {
                // If the link doesn't contain an image move on to the next.
                if ('img' !== $link->firstChild->tagName) {
                    return;
                }
    
                $captionText = '';
                if (0 < $link->parentNode->getElementsByTagName('figcaption')->length) {
                    foreach ($link->parentNode->getElementsByTagName('figcaption') as $i => $caption) {
                        $captionText = wp_strip_all_tags($caption->textContent);
                        $captionClone = $caption->cloneNode(true);
                        $link->parentNode->removeChild($caption);
                    }
                }
    
                $linkedImage = $link->firstChild;
                $imgDir = pathinfo($linkedImage->getAttribute('src'), PATHINFO_DIRNAME);
                $linkDir = pathinfo($link->getAttribute('href'), PATHINFO_DIRNAME);
        
                if ($linkDir === $imgDir) {
                    $altText = $captionText;
                    if (!empty($linkedImage->getAttribute('alt'))) {
                        $altText = $linkedImage->getAttribute('alt');
                    }
                    $html = render_blade_view(
                        'partials.content.image',
                        [
                            'openModal'        => true,
                            'src'              => $linkedImage->getAttribute('src'),
                            'srcFull'          => $linkedImage->getAttribute('src'),
                            'alt'              => $altText,
                            'heading'          => $captionText,
                            'isPanel'          => true,
                            'isTransparent'    => false,
                            'imgAttributeList' =>
                            [
                                'srcset'  => $linkedImage->getAttribute('srcset'),
                                'width'   => $linkedImage->getAttribute('width'),
                                'height'  => $linkedImage->getAttribute('height'),
                                'parsed'  => true
                            ],
                        ]
                    );
                    $newNode = \Municipio\Helper\FormatObject::createNodeFromString($dom, $html);
                    if (empty($newNode)) {
                        continue;
                    }
                    
                    /* Appending the newly rendered blade template content to the current node */
                    $link->parentNode->appendChild($newNode);
                    /* Ensures the existing caption is displayed after the new node */
                    if ($captionClone) {
                        $link->parentNode->appendChild($captionClone);
                    }
                    /* Replacing the original link and image with a hidden link to prevent issues stemming from removing link elements from the DOM whilst accessing them. @see https://stackoverflow.com/questions/38372233/php-domdocument-skips-even-elements */
                    $replacementLink = $dom->createElement('a', $linkedImage->getAttribute('src'));
                    $replacementLink->setAttribute('href', $linkedImage->getAttribute('src'));
                    $replacementLink->setAttribute('tabindex', '-1');
                    $replacementLink->setAttribute('class', 'u-display--none');
                    
                    $link->parentNode->replaceChild($replacementLink, $link);
                }
            }
            
            foreach ($images as $image) {
                /* This image has already been processed so we'll skip it. */
                if ($image->getAttribute('parsed')) {
                    continue;
                }
                $captionText = '';
                if (0 < $image->parentNode->getElementsByTagName('figcaption')->length) {
                    foreach ($image->parentNode->getElementsByTagName('figcaption') as $i => $caption) {
                        $captionText = wp_strip_all_tags($caption->textContent);
                        $captionClone = $caption->cloneNode(true);
                        $image->parentNode->removeChild($caption);
                    }
                }
                $altText = $captionText;
                if (!empty($image->getAttribute('alt'))) {
                    $altText = $image->getAttribute('alt');
                }
                
                $html = render_blade_view(
                    'partials.content.image',
                    [
                        'openModal' => false,
                        'src'       => $image->getAttribute('src'),
                        'alt'       => $altText,
                        'caption' => $captionText,
                        'imgAttributeList' =>
                        [
                            'srcset'  => $image->getAttribute('srcset'),
                            'width'   => $image->getAttribute('width'),
                            'height'  => $image->getAttribute('height'),
                            'parsed'  => true,
                        ],
                    ]
                );
                $newNode = \Municipio\Helper\FormatObject::createNodeFromString($dom, $html);
                $image->parentNode->replaceChild($newNode, $image);
            }
            
            $content = $dom->saveHTML();
        }
        
        
        return $content;
    }
    
   
    /**
     * Append body theme class in BEMIT format
     *
     * @param array $classes Default classes
     *
     * @return array Modified calsses
     */
    public function appendBEMITCssClass($classes)
    {
        if (defined('MUNICIPIO_BEM_THEME_NAME')) {
            $classes[] = MUNICIPIO_BEM_THEME_NAME;
        }
        return $classes;
    }

    /**
     * Adds is-child-theme body class
     *
     * @param array $classes Default classes
     *
     * @return array Modified classes
     */
    public function isChildTheme($classes)
    {
        //Is childtheme class
        if (is_child_theme()) {
            $classes[] = "is-child-theme";
        }
        return $classes;
    }

    /**
     * Filter for adding accessibility items
     *
     * @param array $items Default item array
     *
     * @return array        Modified item array
     */

    /**
     * Filter for adding accessibility items
     * @param $items  Default item array
     * @return array
     */
    public function accessibilityItems($items)
    {
        if (is_single() || is_page()) {
            $items[] =  array(
                'icon' => 'print',
                'href' => '#',
                'script' => 'window.print();return false;',
                'text' => __('Print', 'municipio'),
                'label' => __('Print this page', 'municipio')
            );

            return $items;
        }
    }
}
