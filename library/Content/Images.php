<?php

namespace Municipio\Content;

class Images
{
    public function __construct()
    {
        add_filter('the_content', array($this, 'normalizeImages'), 11);
        add_filter('Municipio/Content/ImageNormalized', array($this, 'imageHasBeenNormalized'), 10, 2);
    }
    /**
     * It takes a string of HTML, finds all images and links containing images, and replaces them with
     * a blade template version of themselves.
     *
     * If an image is linked to itself, it will be replaced with a template version with the attribute `opendModal` set to `true`.
     *
     * If the content contains Gutenberg blocks we'll skip it since images are handled differently then and have their own block type.
     *
     * @param content The content to be parsed.
     *
     * @return The content of the post.
     */
    public static function normalizeImages($content)
    {
        $encoding = '<?xml encoding="utf-8" ?>';

        if ('one-page.blade.php' !== get_page_template_slug() && !has_blocks($content) && str_contains($content, '<img')) {
            $dom = new \DOMDocument();
            $dom->loadHTML($encoding . $content, LIBXML_NOERROR);

            $links = $dom->getElementsByTagName('a');
            if (is_object($links) && !empty($links)) {
                foreach ($links as $link) {
                    //if the link dosent have a child
                    if (!isset($link->firstChild)) {
                        continue;
                    }

                    // If the link doesn't contain an image move on to the next.
                    if ('img' !== $link->firstChild->nodeName) {
                        continue;
                    }

                    $captionText = '';
                    if (0 < $link->parentNode->getElementsByTagName('figcaption')->length) {
                        foreach ($link->parentNode->getElementsByTagName('figcaption') as $i => $caption) {
                            $captionText  = wp_strip_all_tags($caption->textContent);
                            $captionClone = $caption->cloneNode(true);
                            $link->parentNode->removeChild($caption);
                        }
                    }

                    $linkedImage = $link->firstChild;
                    $imgDir      = pathinfo($linkedImage->getAttribute('src'), PATHINFO_DIRNAME);
                    $linkDir     = pathinfo($link->getAttribute('href'), PATHINFO_DIRNAME);

                    if ($linkDir === $imgDir) {
                        $altText = $captionText;
                        if (!empty($linkedImage->getAttribute('alt'))) {
                            $altText = $linkedImage->getAttribute('alt');
                        }

                        $html    = render_blade_view(
                            'partials.content.image',
                            [
                                'openModal'        => true,
                                'src'              => $linkedImage->getAttribute('src'),
                                'srcFull'          => $linkedImage->getAttribute('src'),
                                'alt'              => $altText,
                                'caption'          => $captionText,
                                'isTransparent'    => false,
                                'classList'        => explode(' ', $linkedImage->getAttribute('class')),
                                'imgAttributeList' =>
                                [
                                    'srcset' => $linkedImage->getAttribute('srcset'),
                                    'width'  => $linkedImage->getAttribute('width'),
                                    'height' => $linkedImage->getAttribute('height'),
                                    'parsed' => true
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
                        if (!empty($captionClone)) {
                            $link->parentNode->appendChild($captionClone);
                        }

                        /* Replacing the original link and image with a hidden link to prevent issues stemming from removing link elements from the DOM whilst accessing them. @see https://stackoverflow.com/questions/38372233/php-domdocument-skips-even-elements */
                        $replacementLink = $dom->createElement('a', $linkedImage->getAttribute('src'));
                        $replacementLink->setAttribute('href', $linkedImage->getAttribute('src'));
                        $replacementLink->setAttribute('tabindex', '-1');
                        $replacementLink->setAttribute('class', 'u-display--none');
                        $replacementLink->setAttribute('data-replacement', '1');

                        $link->parentNode->replaceChild($replacementLink, $link);
                    }
                }
            }

            $images = $dom->getElementsByTagName('img');
            
            if (is_object($images) && !empty($images)) {
                foreach ($images as $image) {

                    /**
                     * Filter to check if the image has already been normalized.
                     * @param bool $imageHasBeenNormalized True if the image has already been normalized, false otherwise.
                     * @param DOMElement $image The image element.
                     * @return bool
                     */
                    $imageHasBeenNormalized = apply_filters('Municipio/Content/ImageNormalized', false, $image);

                    if ($imageHasBeenNormalized) {
                        continue;
                    }

                    $captionText = '';
                    if (0 < $image->parentNode->getElementsByTagName('figcaption')->length) {
                        foreach ($image->parentNode->getElementsByTagName('figcaption') as $i => $caption) {
                            $captionText  = wp_strip_all_tags($caption->textContent);
                            $captionClone = $caption->cloneNode(true);
                            $image->parentNode->removeChild($caption);
                        }
                    }
                    $altText = $captionText;
                    if (!empty($image->getAttribute('alt'))) {
                        $altText = $image->getAttribute('alt');
                    }

                    $html    = render_blade_view(
                        'partials.content.image',
                        [
                            'openModal'        => false,
                            'src'              => $image->getAttribute('src'),
                            'alt'              => $altText,
                            'caption'          => $captionText,
                            'classList'        => explode(' ', $image->getAttribute('class')),
                            'imgAttributeList' =>
                            [
                                'srcset' => $image->getAttribute('srcset'),
                                'width'  => $image->getAttribute('width'),
                                'height' => $image->getAttribute('height'),
                                'parsed' => true,
                            ],
                        ]
                    );

                    var_dump($image->getAttribute('srcset'));

                    $newNode = \Municipio\Helper\FormatObject::createNodeFromString($dom, $html);
                    $image->parentNode->replaceChild($newNode, $image);
                }

                /* Removing the hidden links that were added earlier, now that the iteration of the elements is done */
                foreach ($dom->getElementsByTagName('a') as $link) {
                    $isReplacement = (bool) $link->getAttribute('data-replacement');
                    if ($isReplacement) {
                        $link->parentNode->removeChild($link);
                    }
                }

                $content = $dom->saveHTML();

                return str_replace([$encoding, '<html>', '</html>', '<body>', '</body>'], '', \Municipio\Helper\Post::replaceBuiltinClasses($content));
            }
        }

        return $content;
    }

    public static function imageHasBeenNormalized($normalized, $image): bool
    {
        if ($image->getAttribute('parsed')) {
            return true;
        }

        if (strpos($image->getAttribute('class'), 'c-image__image') !== false) {
            return true;
        }

        return $normalized;
    }
}
