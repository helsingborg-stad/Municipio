<?php

namespace Municipio\ImageFocus\Resolvers;

use Municipio\ImageFocus\Resolvers\FocusPointResolverInterface;

/**
 * Example resolver for an external AI service (not implemented)
 * This is a placeholder and does not contain actual implementation.
 * 
 * If any external AI service is to be integrated, the logic can be 
 * added here.
 */

class ExternalAIFocusPointResolver implements FocusPointResolverInterface
{
    public function __construct(private $aiService) {}

    public function isSupported(): bool
    {
        return false;
    }

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        return null;
    }
}