<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\Icon\IconInterface;
use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetPostMeta;

/**
 * PostObjectWithSeoRedirect class.
 *
 * Applies the SEO redirect to the post object permalink if a redirect is set.
 */
class PostObjectWithSeoRedirect extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(PostObjectInterface $postObject, private GetPostMeta $wpService)
    {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        $seoRedirectMetaUrl = $this->wpService->getPostMeta($this->postObject->getId(), 'redirect', true);

        if (filter_var($seoRedirectMetaUrl, FILTER_VALIDATE_URL)) {
            return $seoRedirectMetaUrl;
        }

        return $this->postObject->getPermalink();
    }
}
