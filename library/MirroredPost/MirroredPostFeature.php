<?php

namespace Municipio\MirroredPost;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use Municipio\MirroredPost\PostObject\MirroredPostObject;
use Municipio\MirroredPost\Utils\GetOtherBlogId\GetOtherBlogId;
use Municipio\MirroredPost\Utils\IsMirroredPost\IsMirroredPost;
use Municipio\MirroredPost\Utils\MirroredPostUtils;
use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\PostObject\PostObjectInterface;
use WpService\WpService;

/**
 * Class MirroredPostFeature
 *
 * This class is responsible for enabling the Mirrored Post feature in WordPress.
 * It sets up the necessary hooks and filters to handle mirrored posts.
 */
class MirroredPostFeature
{
    /**
     * Constructor.
     */
    public function __construct(private WpService $wpService)
    {
    }

    /**
     * Enable the Mirrored Post feature.
     *
     * This method sets up the necessary hooks and filters to handle mirrored posts.
     * It adds a query variable for the blog ID and enables the single mirrored post in WordPress.
     */
    public function enable(): void
    {
        (new BlogIdQueryVar($this->wpService))->addHooks();
        (new EnableSingleMirroredPostInWpQuery($this->wpService, $this->getUtils()))->addHooks();

        // Possibly add PostObject decorator.
        $this->wpService->addFilter(CreatePostObjectFromWpPost::DECORATE_FILTER_NAME, function (PostObjectInterface $postObject): PostObjectInterface {
            $otherBlogId = $this->getUtils()->getOtherBlogId();

            return $otherBlogId !== null
            ? new MirroredPostObject($postObject, $this->wpService, $otherBlogId)
            : $postObject;
        });
    }

    /**
     * Get the MirroredPostUtils instance.
     *
     * @return MirroredPostUtils
     */
    private function getUtils(): MirroredPostUtils
    {
        static $mirroredPostUtils = null;

        if (!$mirroredPostUtils) {
            $mirroredPostUtils = new MirroredPostUtils(
                new IsMirroredPost($this->wpService),
                new GetOtherBlogId($this->wpService)
            );
        }

        return $mirroredPostUtils;
    }
}
