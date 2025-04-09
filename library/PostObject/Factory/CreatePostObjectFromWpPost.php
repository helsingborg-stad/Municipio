<?php

namespace Municipio\PostObject\Factory;

use AcfService\AcfService;
use Municipio\Helper\StringToTime;
use Municipio\Helper\Term\Term;
use Municipio\PostObject\Date\ArchiveDateFormatResolver;
use Municipio\PostObject\Date\ArchiveDateSourceResolver;
use Municipio\PostObject\Date\CachedArchiveDateFormatResolver;
use Municipio\PostObject\Date\CachedArchiveDateSourceResolver;
use Municipio\PostObject\Date\CachedTimestampResolver;
use Municipio\PostObject\Date\TimestampResolver;
use Municipio\PostObject\Decorators\BackwardsCompatiblePostObject;
use Municipio\PostObject\Decorators\IconResolvingPostObject;
use Municipio\PostObject\Decorators\PostObjectArchiveDateFormat;
use Municipio\PostObject\Decorators\PostObjectArchiveDateTimestamp;
use Municipio\PostObject\Decorators\PostObjectFromOtherBlog;
use Municipio\PostObject\Decorators\PostObjectFromWpPost;
use Municipio\PostObject\Decorators\PostObjectWithSeoRedirect;
use Municipio\PostObject\Icon\Resolvers\CachedIconResolver;
use Municipio\PostObject\Icon\Resolvers\NullIconResolver;
use Municipio\PostObject\Icon\Resolvers\PostIconResolver;
use Municipio\PostObject\Icon\Resolvers\TermIconResolver;
use Municipio\PostObject\PostObject;
use Municipio\PostObject\PostObjectInterface;
use WpService\WpService;

class CreatePostObjectFromWpPost implements PostObjectFromWpPostFactoryInterface
{
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService
    ) {
    }

    public function create(\WP_Post $post): PostObjectInterface
    {
        $camelCasedPost = \Municipio\Helper\FormatObject::camelCase($post);

        $postObject = new PostObject($this->wpService);
        $postObject = new PostObjectFromWpPost($postObject, $post, $this->wpService);
        $postObject = new PostObjectWithSeoRedirect($postObject, $this->wpService);

        $archiveDateFormatResolver = new ArchiveDateFormatResolver($postObject, $this->wpService);
        $archiveDateFormatResolver = new CachedArchiveDateFormatResolver($postObject, $archiveDateFormatResolver);
        $postObject                = new PostObjectArchiveDateFormat($postObject, $archiveDateFormatResolver);

        $archiveDateSourceResolver = new ArchiveDateSourceResolver($postObject, $this->wpService);
        $archiveDateSourceResolver = new CachedArchiveDateSourceResolver($postObject, $archiveDateSourceResolver);
        $stringToTimeHelper        = new StringToTime($this->wpService);
        $timestampResolver         = new TimestampResolver($postObject, $this->wpService, $archiveDateSourceResolver, $stringToTimeHelper);
        $timestampResolver         = new CachedTimestampResolver($postObject, $this->wpService, $timestampResolver);

        $postObject = new PostObjectArchiveDateTimestamp($postObject, $timestampResolver);

        $iconResolver = new TermIconResolver($postObject, $this->wpService, new Term($this->wpService, $this->acfService), new NullIconResolver());
        $iconResolver = new PostIconResolver($postObject, $this->acfService, $iconResolver);
        $iconResolver = new CachedIconResolver($postObject, $iconResolver);

        $postObject = new IconResolvingPostObject($postObject, $iconResolver);

        if ($this->wpService->isMultiSite() && $this->wpService->msIsSwitched()) {
            $postObject = new PostObjectFromOtherBlog($postObject, $this->wpService, $this->wpService->getCurrentBlogId());
        }

        $postObject = new BackwardsCompatiblePostObject($postObject, $camelCasedPost);

        return $postObject;
    }
}
