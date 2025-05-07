<?php

namespace Municipio\PostObject\Factory;

use AcfService\AcfService;
use Municipio\Helper\StringToTime;
use Municipio\Helper\Term\Term;
use Municipio\PostObject\Date\{ArchiveDateFormatResolver, ArchiveDateFormatResolverInterface, ArchiveDateSourceResolver, ArchiveDateSourceResolverInterface, CachedArchiveDateFormatResolver, CachedArchiveDateSourceResolver, CachedTimestampResolver, TimestampResolver, TimestampResolverInterface};
use Municipio\PostObject\Icon\Resolvers\{CachedIconResolver, IconResolverInterface, NullIconResolver, PostIconResolver, TermIconResolver};
use Municipio\PostObject\{PostObject, PostObjectInterface};
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use WpService\WpService;
use Municipio\PostObject\Decorators\{
    BackwardsCompatiblePostObject,
    IconResolvingPostObject,
    PostObjectArchiveDateFormat,
    PostObjectArchiveDateTimestamp,
    PostObjectFromOtherBlog,
    PostObjectFromWpPost,
    PostObjectWithSchemaObject,
    PostObjectWithSeoRedirect
};

/**
 * CreatePostObjectFromWpPost.
 *
 * Create a PostObject from a WP_Post object.
 */
class CreatePostObjectFromWpPost implements PostObjectFromWpPostFactoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private SchemaObjectFromPostInterface $schemaObjectFromPost
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(\WP_Post $post): PostObjectInterface
    {
        $camelCasedPost = \Municipio\Helper\FormatObject::camelCase($post);

        $postObject = new PostObject($post->ID, $this->wpService);
        $postObject = new PostObjectFromWpPost($postObject, $post, $this->wpService);
        $postObject = new PostObjectWithSeoRedirect($postObject, $this->wpService);
        $postObject = new PostObjectArchiveDateFormat($postObject, $this->getArchiveDateFormatResolver($postObject));
        $postObject = new PostObjectArchiveDateTimestamp($postObject, $this->getTimestampResolver($postObject));
        $postObject = new IconResolvingPostObject($postObject, $this->getIconResolver($postObject));

        if ($this->wpService->isMultiSite() && $this->wpService->msIsSwitched()) {
            $postObject = new PostObjectFromOtherBlog($postObject, $this->wpService, $this->wpService->getCurrentBlogId());
        }

        $postObject = new PostObjectWithSchemaObject($postObject, $this->schemaObjectFromPost);
        $postObject = new BackwardsCompatiblePostObject($postObject, $camelCasedPost);

        return $postObject;
    }

    /**
     * Get the archive date format resolver.
     *
     * @param PostObjectInterface $postObject
     * @return ArchiveDateFormatResolverInterface
     */
    private function getArchiveDateFormatResolver(PostObjectInterface $postObject): ArchiveDateFormatResolverInterface
    {
        $archiveDateFormatResolver = new ArchiveDateFormatResolver($postObject, $this->wpService);
        $archiveDateFormatResolver = new CachedArchiveDateFormatResolver($postObject, $archiveDateFormatResolver);

        return $archiveDateFormatResolver;
    }

    /**
     * Get the timestamp resolver.
     *
     * @param PostObjectInterface $postObject
     * @return TimestampResolverInterface
     */
    private function getTimestampResolver(PostObjectInterface $postObject): TimestampResolverInterface
    {
        $timestampResolver = new TimestampResolver($postObject, $this->wpService, $this->getArchiveDateSourceResolver($postObject), new StringToTime($this->wpService));
        $timestampResolver = new CachedTimestampResolver($postObject, $this->wpService, $timestampResolver);

        return $timestampResolver;
    }

    /**
     * Get the archive date source resolver.
     *
     * @param PostObjectInterface $postObject
     * @return ArchiveDateSourceResolverInterface
     */
    private function getArchiveDateSourceResolver(PostObjectInterface $postObject): ArchiveDateSourceResolverInterface
    {
        $archiveDateSourceResolver = new ArchiveDateSourceResolver($postObject, $this->wpService);
        $archiveDateSourceResolver = new CachedArchiveDateSourceResolver($postObject, $archiveDateSourceResolver);

        return $archiveDateSourceResolver;
    }

    /**
     * Get the icon resolver.
     *
     * @param PostObjectInterface $postObject
     * @return IconResolverInterface
     */
    private function getIconResolver(PostObjectInterface $postObject): IconResolverInterface
    {
        $iconResolver = new TermIconResolver($postObject, $this->wpService, new Term($this->wpService, $this->acfService), new NullIconResolver());
        $iconResolver = new PostIconResolver($postObject, $this->acfService, $iconResolver);
        $iconResolver = new CachedIconResolver($postObject, $iconResolver);

        return $iconResolver;
    }
}
