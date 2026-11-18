<?php

namespace Municipio\Content\Images;

use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Helper\RenderBladeView\RenderBladeView;
use Municipio\Helper\RenderBladeView\RenderBladeViewInterface;
use Municipio\Integrations\Component\ImageResolver;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\AttachmentUrlToPostid;
use WpService\Contracts\GetPostField;
use WpService\Contracts\GetTheID;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\HasBlocks;
use WpService\Contracts\WpGetAttachmentImageSrc;
use WpService\Contracts\WpStripAllTags;

class ImagesDeprecated implements ImagesInterface
{
    public function __construct(
        private AddAction&AddFilter&ApplyFilters&AttachmentUrlToPostid&GetPostField&GetTheID&GetThemeMod&HasBlocks&WpGetAttachmentImageSrc&WpStripAllTags $wpService,
        private RenderBladeViewInterface $renderBladeView = new RenderBladeView(),
    ) {
        $this->wpService->addAction('wp', function () {
            $postId    = $this->wpService->getTheID();
            $hasBlocks = $postId !== false && $this->wpService->hasBlocks($this->wpService->getPostField('post_content', $postId));

            $this->wpService->addFilter('the_content', [$this, 'normalizeImages'], $hasBlocks ? 5 : 11);
        });

        $this->wpService->addFilter('Municipio/Content/ImageNormalized', [$this, 'imageHasBeenNormalized'], 10, 2);
    }

    public function normalizeImages($content)
    {
        $encoding = '<?xml encoding="utf-8" ?>';

        if (!$this->wpService->hasBlocks($content) && str_contains($content, '<img')) {
            $dom = new \DOMDocument();
            $dom->loadHTML($encoding . $content, LIBXML_NOERROR);
            $xpath = new \DOMXPath($dom);

            $links = $dom->getElementsByTagName('a');
            $images = $xpath->query('//img[contains(@class, "wp-image-")]');

            $this->processLinks($dom, $links);
            $this->processImages($dom, $images);

            $content = $dom->saveHTML();

            return str_replace(
                [$encoding, '<html>', '</html>', '<body>', '</body>'],
                '',
                \Municipio\Helper\Post::replaceBuiltinClasses($content),
            );
        }

        return $content;
    }

    private function processLinks($dom, $links)
    {
        if (!is_object($links) || empty($links))
            return;

        foreach ($links as $link) {
            if (!isset($link->firstChild) || $link->firstChild->nodeName !== 'img')
                continue;

            $linkedImage = $link->firstChild;
            if ($this->isSelfLinked($link, $linkedImage)) {
                $linkedImage->setAttribute('parsed', '1');
                $captionText = $this->extractCaption($link->parentNode);
                $altText = $linkedImage->getAttribute('alt') ?: $captionText;
                $this->replaceWithBladeTemplate($dom, $link, $linkedImage, $altText, $captionText);
            }
        }
    }

    private function processImages($dom, $images)
    {
        if (!is_object($images) || empty($images))
            return;

        foreach ($images as $image) {
            if ($this->wpService->applyFilters('Municipio/Content/ImageNormalized', false, $image))
                continue;

            $captionText = $this->extractCaption($image->parentNode);
            $altText = $image->getAttribute('alt') ?: $captionText;

            $this->replaceWithBladeTemplate($dom, $image, $image, $altText, $captionText);
        }
    }

    private function isSelfLinked($link, $image)
    {
        $imgDir = pathinfo($image->getAttribute('src'), PATHINFO_DIRNAME);
        $linkDir = pathinfo($link->getAttribute('href'), PATHINFO_DIRNAME);
        return $linkDir === $imgDir;
    }

    public function extractCaption($parentNode)
    {
        $captionText = '';

        if ($parentNode instanceof \DOMElement && $parentNode->getElementsByTagName('figcaption')->length > 0) {
            foreach ($parentNode->getElementsByTagName('figcaption') as $caption) {
                $captionText = $this->wpService->wpStripAllTags($caption->textContent);

                if ($caption->parentNode instanceof \DOMNode) {
                    $caption->parentNode->removeChild($caption);
                }
            }
        }

        return $captionText;
    }

    private function replaceWithBladeTemplate($dom, $element, $image, $altText, $captionText)
    {
        $url = $this->sanitizeRequestUrl($image->getAttribute('src'));
        $attachmentId = $this->wpService->attachmentUrlToPostid($url);

        $captionFigure = CaptionFigure::find($image);
        $wrapper       = $captionFigure ?? $image->parentNode;

        $classes = ImageAlignment::resolveClassList(
            $wrapper instanceof \DOMElement
                ? ImageAlignment::splitClassAttribute($wrapper->getAttribute('class'))
                : [],
            ImageAlignment::splitClassAttribute($image->getAttribute('class'))
        );
        $classes[] = ImageAlignment::CONTENT_IMAGE_CLASS;

        if (is_numeric($attachmentId) && !empty($attachmentId)) {
            $contentContainerWidth = $this->getPageWidth();
            $imageSrc = $this->wpService->wpGetAttachmentImageSrc($attachmentId, [$contentContainerWidth, false]);

            if ($imageSrc && isset($imageSrc[0])) {
                $html = $this->renderBladeView->renderBladeView('partials.content.image', [
                    'src' => ImageComponentContract::factory(
                        (int) $attachmentId,
                        [$contentContainerWidth, false],
                        new ImageResolver()
                    ),
                    'alt' => $altText,
                    'caption' => $captionText,
                    'classList' => $classes,
                    'imgAttributeList' => ['parsed' => true],
                    'attributeList' => ImageAlignment::getAttributeList($image->getAttribute('width')),
                ]);
            }
        }

        if (empty($attachmentId)) {
            $html = $this->renderBladeView->renderBladeView('partials.content.image', [
                'src' => $url,
                'alt' => $altText,
                'caption' => $captionText,
                'classList' => $classes,
                'imgAttributeList' => ['parsed' => true],
                'attributeList' => ImageAlignment::getAttributeList($image->getAttribute('width')),
            ]);
        }

        if (!empty($html)) {
            $newNode = \Municipio\Helper\FormatObject::createNodeFromString($dom, $html);
            if ($image)
                $image->replaceWith($newNode);

            if ($captionFigure !== null) {
                CaptionFigure::unwrap($captionFigure);
            }
        }
    }

    public function imageHasBeenNormalized($normalized, $image): bool
    {
        return $image->getAttribute('parsed') ? true : $normalized;
    }

    private function sanitizeRequestUrl($url): string
    {
        $parsedUrl = parse_url($url);
        if (!$parsedUrl)
            return $url;

        $sanitizedUrl = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $sanitizedUrl .= isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $sanitizedUrl .= isset($parsedUrl['port']) && !in_array($parsedUrl['port'], [80, 443]) ? ':' . $parsedUrl['port'] : '';
        $sanitizedUrl .= isset($parsedUrl['path']) ? $parsedUrl['path'] : '';

        return preg_replace('/-(\d+x\d+)(?=\.\w{3,4}$)/', '', $sanitizedUrl);
    }

    private function getPageWidth(): int
    {
        return (int) ceil($this->wpService->getThemeMod('container_content', 900) / 100) * 100;
    }
}
