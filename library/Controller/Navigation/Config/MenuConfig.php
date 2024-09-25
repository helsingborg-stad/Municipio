<?php

namespace Municipio\Controller\Navigation;

class MenuConfig
{
    public function __construct(
        public string $identifier = '',
        public string $menuName = '',
        public ?int $pageId = null,
        public $wpdb = null,
        public bool $fallbackToPageTree = false,
        public bool $includeTopLevel = true,
        public bool $onlyKeepFirstLevel = false,
        public $context = 'municipio'
    ) {
    }
}