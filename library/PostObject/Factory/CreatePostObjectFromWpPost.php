<?php

namespace Municipio\PostObject\Factory;

use AcfService\AcfService;
use Municipio\Helper\StringToTime;
use Municipio\Helper\Term\Term;
use Municipio\PostObject\Date\ArchiveDateFormatResolver;
use Municipio\PostObject\Date\ArchiveDateFormatResolverInterface;
use Municipio\PostObject\Date\ArchiveDateSourceResolver;
use Municipio\PostObject\Date\ArchiveDateSourceResolverInterface;
use Municipio\PostObject\Date\CachedArchiveDateSourceResolver;
use Municipio\PostObject\Date\CachedTimestampResolver;
use Municipio\PostObject\Date\ExhibitionEventArchiveDateFormatResolver;
use Municipio\PostObject\Date\TimestampResolver;
use Municipio\PostObject\Date\TimestampResolverInterface;
use Municipio\PostObject\Decorators\BackwardsCompatiblePostObject;
use Municipio\PostObject\Decorators\IconResolvingPostObject;
use Municipio\PostObject\Decorators\PostObjectArchiveDateFormat;
use Municipio\PostObject\Decorators\PostObjectArchiveDateTimestamp;
use Municipio\PostObject\Decorators\PostObjectFromWpPost;
use Municipio\PostObject\Decorators\PostObjectUsingExcerptResolver;
use Municipio\PostObject\Decorators\PostObjectWithCachedContent;
use Municipio\PostObject\Decorators\PostObjectWithFilteredContent;
use Municipio\PostObject\Decorators\PostObjectWithSchemaObject;
use Municipio\PostObject\Decorators\PostObjectWithSeoRedirect;
use Municipio\PostObject\ExcerptResolver\ExcerptResolver;
use Municipio\PostObject\Icon\Resolvers\CachedIconResolver;
use Municipio\PostObject\Icon\Resolvers\IconResolverInterface;
use Municipio\PostObject\Icon\Resolvers\NullIconResolver;
use Municipio\PostObject\Icon\Resolvers\PostIconResolver;
use Municipio\PostObject\Icon\Resolvers\TermIconResolver;
use Municipio\PostObject\PostObject;
use Municipio\PostObject\PostObjectInterface;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use WpService\WpService;

/**
 * CreatePostObjectFromWpPost.
 *
 * Create a PostObject from a WP_Post object.
 */
class CreatePostObjectFromWpPost implements PostObjectFromWpPostFactoryInterface
{
    public const DECORATE_FILTER_NAME = 'Municipio/DecoratePostObject';

    /**
     * Constructor.
     */
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private SchemaObjectFromPostInterface $schemaObjectFromPost,
    ) {}

    /**
     * @inheritDoc
     */
    public function create(\WP_Post $post): PostObjectInterface
    {
        $camelCasedPost = \Municipio\Helper\FormatObject::camelCase($post);

        $postObject = new PostObject($post->ID, $this->wpService);
        $postObject = new PostObjectFromWpPost($postObject, $post, $this->wpService);
        $postObject = new PostObjectUsingExcerptResolver($postObject, new ExcerptResolver($this->wpService));
        $postObject = new PostObjectWithFilteredContent($postObject, $this->wpService);
        $postObject = new PostObjectWithSeoRedirect($postObject, $this->wpService);
        $postObject = new PostObjectWithSchemaObject($postObject, $this->schemaObjectFromPost);
        $postObject = new PostObjectArchiveDateFormat($postObject, $this->getArchiveDateFormatResolver($postObject));
        $postObject = new PostObjectArchiveDateTimestamp($postObject, $this->getTimestampResolver($postObject));
        $postObject = new IconResolvingPostObject($postObject, $this->getIconResolver($postObject));
        $postObject = new PostObjectWithCachedContent($postObject, $this->wpService);
        $postObject = new BackwardsCompatiblePostObject($postObject, $camelCasedPost);

        $postObject = $this->wpService->applyFilters(self::DECORATE_FILTER_NAME, $postObject);

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
        return new ExhibitionEventArchiveDateFormatResolver($postObject, $this->wpService, $archiveDateFormatResolver);
    }

    /**
     * Get the timestamp resolver.
     *
     * @param PostObjectInterface $postObject
     * @return TimestampResolverInterface
     */
    private function getTimestampResolver(PostObjectInterface $postObject): TimestampResolverInterface
    {
        $timestampResolver = new TimestampResolver(
            $postObject,
            $this->wpService,
            $this->getArchiveDateSourceResolver($postObject),
            new StringToTime($this->wpService),
        );
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
        $iconResolver = new TermIconResolver(
            $postObject,
            $this->wpService,
            new Term($this->wpService, $this->acfService),
            new NullIconResolver(),
        );
        $iconResolver = new PostIconResolver($postObject, $this->acfService, $iconResolver);
        $iconResolver = new CachedIconResolver($postObject, $iconResolver);

        return $iconResolver;
    }
}
