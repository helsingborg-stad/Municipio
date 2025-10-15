<?php

namespace Modularity\Module\Posts\Helper;

class DomainChecker
{
    private string|null $currentDomain = null;

    public function __construct(array $fields = [])
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            $hostParts = explode(':', $_SERVER['HTTP_HOST']);
            $this->currentDomain = strtolower($hostParts[0]);
        } else {
            $this->currentDomain = null;
        }
    }

    public function isSameDomain(string $url): bool
    {
        if ($this->currentDomain === null) {
            return false;
        }
        
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['host'])) {
            return false;
        }

        return $this->currentDomain === strtolower($parsedUrl['host']);
    }
}