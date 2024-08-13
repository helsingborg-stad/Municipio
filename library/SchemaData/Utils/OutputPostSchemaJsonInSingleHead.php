<?php

namespace Municipio\SchemaData\Utils;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetPost;
use WpService\Contracts\IsSingle;

class OutputPostSchemaJsonInSingleHead implements Hookable
{
    public function __construct(
        private SchemaObjectFromPostInterface $schemaJsonFromPost, 
        private AddAction&IsSingle&GetPost $wpService
        )
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_head', [$this, 'print']);
    }

    public function print(): void
    {
        if ($this->wpService->isSingle()) {
            echo ($this->schemaJsonFromPost->create($this->wpService->getPost()))->toScript();
        }
    }
}
