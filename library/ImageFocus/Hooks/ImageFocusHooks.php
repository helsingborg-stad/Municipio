<?php

namespace Municipio\ImageFocus\Hooks;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageFocus\ImageFocusManager;
use WpService\WpService;

class ImageFocusHooks implements Hookable
{
    public function __construct(private WpService $wpService, private ImageFocusManager $manager) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_generate_attachment_metadata', [$this, 'handle'], 10, 3);
    }

    public function handle($metadata, $attachmentId, $context)
    {
        return $this->manager->calculate($attachmentId, $metadata, $context);
    }
}