<?php

namespace Municipio\ImageConvert\Strategy;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\ImageProcessor;
use WpService\Contracts\GetCurrentUserId;
use WpService\Contracts\GetPost;
use WpService\Contracts\UserCan;
use Municipio\ImageConvert\Config\ImageConvertConfig;

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
        private GetCurrentUserId&UserCan&GetPost $wpService,
        private ImageConvertConfig $config,
        private ImageProcessor $imageProcessor,
        private BackgroundConversionStrategy $backgroundStrategy
    ) {
    }

    /**
     * Process the image using either immediate processing or background strategy based on context
     * 
     * @param ImageContract $image The image to process
     * @return ImageContract|false The processed image or false on failure
     */
    public function process(ImageContract $image): ImageContract|false
    {
        if ($this->shouldProcessImmediately($image)) {
            return $this->imageProcessor->process($image);
        }
        return $this->backgroundStrategy->process($image);
    }

    /**
     * Get the strategy name/identifier
     * 
     * @return string
     */
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
        $currentUserId = $this->wpService->getCurrentUserId();

        if (!$currentUserId || !$this->wpService->userCan($currentUserId, 'edit_posts')) {
            return false;
        }

        $postId = $this->getCurrentPostId();
        if (!$postId) {
            return false;
        }

        return (time() - $this->getCurrentPostModifiedTime()) < $this->config->mixedStrategyEditorTimeframeSeconds();
    }

    /**
     * Get the current post ID if available
     * 
     * @return int|null
     */
    private function getCurrentPostId(): int|null
    {
        $post = $this->wpService->getPost(get_the_ID());
        return $post ? $post->ID : null;
    }

    /**
     * Get the current post modified time if available
     * 
     * @return int|null
     */
    private function getCurrentPostModifiedTime(): int|null
    {
        $post = $this->wpService->getPost(get_the_ID());
        return $post ? strtotime($post->post_modified_gmt) : null;
    }
}