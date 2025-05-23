<?php

namespace Municipio\MirroredPost\Utils\GetOtherBlogId;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use WpService\Contracts\{GetCurrentBlogId, GetQueryVar, IsMultisite, MsIsSwitched};

class GetOtherBlogId implements GetOtherBlogIdInterface
{
    public function __construct(private IsMultisite&MsIsSwitched&GetQueryVar&GetCurrentBlogId $wpService)
    {
    }

    public function getOtherBlogId(): ?int
    {
        if ($this->wpService->isMultiSite() && $this->wpService->msIsSwitched()) {
            return $this->wpService->getCurrentBlogId();
        }

        $blogId = $this->wpService->getQueryVar(BlogIdQueryVar::BLOG_ID_QUERY_VAR, null);
        $postId = $this->wpService->getQueryVar('p', null);

        if ($this->wpService->isMultiSite() && $postId) {
            return (int) $blogId;
        }

        return null;
    }
}
