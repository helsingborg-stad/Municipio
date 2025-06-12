<?php

namespace Municipio\MirroredPost\Utils\GetOtherBlogId;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use WpService\Contracts\{GetCurrentBlogId, GetQueryVar, IsMultisite, MsIsSwitched};

/**
 * Class GetOtherBlogId
 *
 * Implements the GetOtherBlogIdInterface to provide functionality for retrieving
 * the ID of another blog within a multisite WordPress installation.
 */
class GetOtherBlogId implements GetOtherBlogIdInterface
{
    /**
     * Constructor for the GetOtherBlogId class.
     */
    public function __construct(private IsMultisite&MsIsSwitched&GetQueryVar&GetCurrentBlogId $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function getOtherBlogId(): ?int
    {
        if ($this->wpService->isMultiSite() && $this->wpService->msIsSwitched()) {
            return $this->wpService->getCurrentBlogId();
        }

        $blogId = $this->wpService->getQueryVar(BlogIdQueryVar::BLOG_ID_QUERY_VAR, null);
        $postId = $this->wpService->getQueryVar('p', null);

        if ($this->wpService->isMultiSite() && !is_null($postId) && !is_null($blogId)) {
            return (int) $blogId;
        }

        return null;
    }
}
