<?php

namespace Municipio\SchemaData\FeatureRequirements;

use Municipio\IniService\IniServiceInterface;

class SystemReadDocCommentFeatureRequirement implements FeatureRequirement
{
    public function __construct(private IniServiceInterface $iniService)
    {
    }

    public function isMet(): bool
    {
        if ($this->opCachePreventsReadingComments()) {
            return false;
        }

        return true;
    }

    private function opCachePreventsReadingComments(): bool
    {
        if (
            $this->iniService->get('opcache.enable') !== "1" &&
            $this->iniService->get('opcache.enable') !== "On"
        ) {
            // OPcache is not enabled, so it can't prevent reading of doc comments.
            return false;
        }

        if (
            $this->iniService->get('opcache.save_comments') === 'On' ||
            $this->iniService->get('opcache.save_comments') === '1'
        ) {
            // OPcache is enabled, but it does not remove comments.
            return false;
        }

        return true;
    }
}
