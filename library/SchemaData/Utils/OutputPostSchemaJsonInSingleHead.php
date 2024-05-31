<?php

namespace Municipio\SchemaData\Utils;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\SchemaJsonFromPost\SchemaJsonFromPostInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetPost;
use WpService\Contracts\IsSingle;

class OutputPostSchemaJsonInSingleHead implements Hookable
{
    public function __construct(
        private SchemaJsonFromPostInterface $schemaJsonFromPost, 
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
            echo '<script type="application/ld+json">' . $this->schemaJsonFromPost->create($this->wpService->getPost()) . '</script>';
        }
    }
}
