<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\ConversionCache;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Contracts\WpGetImageEditor;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpAttachmentIs;
use WpService\Contracts\AddFilter;
use WpService\Contracts\DoAction;
use WpService\Contracts\GetCurrentUserId;
use WpService\Contracts\GetPost;
use WpService\Contracts\UserCan;
use WpService\Contracts\GetPostMeta;

/**
 * Mixed Conversion Strategy
 * 
 * Combines runtime and background processing based on user context.
 * If the current user is an editor who made recent changes to the post/image,
 * processes immediately. Otherwise, queues for background processing.
 */
class MixedConversionStrategy implements ConversionStrategyInterface
{
    public function __construct(
        private WpGetImageEditor&IsWpError&WpGetAttachmentMetadata&WpAttachmentIs&AddFilter&DoAction&GetCurrentUserId&UserCan&GetPostMeta&GetPost $wpService,
        private ImageConvertConfig $config,
        private ConversionCache $conversionCache,
        private RuntimeConversionStrategy $runtimeStrategy,
        private BackgroundConversionStrategy $backgroundStrategy
    ) {
    }

    public function process(ImageContract $image): ImageContract|false
    {
        // Check if this should be processed immediately based on editor context
        if ($this->shouldProcessImmediately($image)) {
            // Use runtime strategy for immediate processing
            return $this->runtimeStrategy->process($image);
        }

        // Use background strategy for deferred processing
        return $this->backgroundStrategy->process($image);
    }

    public function canHandle(ImageContract $image): bool
    {
        // Mixed strategy can handle any image resize request
        return true;
    }

    public function getName(): string
    {
        return 'mixed';
    }

    /**
     * Determine if the image should be processed immediately based on editor context
     * 
     * @param ImageContract $image
     * @return bool
     */
    private function shouldProcessImmediately(ImageContract $image): bool
    {
        // Get current user
        $currentUserId = $this->wpService->getCurrentUserId();
        
        // Check if user is logged in
        if (!$currentUserId) {
            return false;
        }

        // Check if user has editor capabilities
        if (!$this->wpService->userCan($currentUserId, 'edit_posts')) {
            return false;
        }

        // Get the attachment post to check recent modifications
        $attachment = $this->wpService->getPost($image->getId());
        if (!$attachment) {
            return false;
        }

        // Check if the current user modified this attachment recently (within last hour)
        if ($this->hasUserModifiedImageRecently($image->getId(), $currentUserId)) {
            return true;
        }

        // Check if the current user modified the parent post recently
        if ($attachment->post_parent && $this->hasUserModifiedPostRecently($attachment->post_parent, $currentUserId)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the current user modified the image recently
     * 
     * @param int $imageId
     * @param int $userId
     * @return bool
     */
    private function hasUserModifiedImageRecently(int $imageId, int $userId): bool
    {
        $attachment = $this->wpService->getPost($imageId);
        if (!$attachment) {
            return false;
        }

        // Check if this user was the last to modify the attachment
        if ((int) $attachment->post_author !== $userId) {
            return false;
        }

        // Check if modification was within the last hour
        $modifiedTime = strtotime($attachment->post_modified_gmt);
        $oneHourAgo = time() - 3600;

        return $modifiedTime > $oneHourAgo;
    }

    /**
     * Check if the current user modified the parent post recently
     * 
     * @param int $postId
     * @param int $userId
     * @return bool
     */
    private function hasUserModifiedPostRecently(int $postId, int $userId): bool
    {
        $post = $this->wpService->getPost($postId);
        if (!$post) {
            return false;
        }

        // Check if this user was the last to modify the post
        if ((int) $post->post_author !== $userId) {
            // Also check if user has edit permission for this specific post
            if (!$this->wpService->userCan($userId, 'edit_post', $postId)) {
                return false;
            }
        }

        // Check if modification was within the last hour
        $modifiedTime = strtotime($post->post_modified_gmt);
        $oneHourAgo = time() - 3600;

        return $modifiedTime > $oneHourAgo;
    }
}