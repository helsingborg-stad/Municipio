<?php

namespace Municipio\SchemaData\Utils;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;
use WpService\Contracts\GetPost;
use WpService\Contracts\IsSingle;

class OutputPostSchemaJsonInSingleHead implements Hookable
{
    public function __construct(
        private SchemaObjectFromPostInterface $schemaJsonFromPost,
        private AddAction&IsSingle&GetPost&DoAction $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_head', [$this, 'print']);
    }

    public function print(): void
    {
        if ($this->wpService->isSingle()) {
            $schema = $this->schemaJsonFromPost->create($this->wpService->getPost());

            echo $schema->toScript();
            $this->wpService->doAction('Municipio\SchemaData\OutputPostSchemaJsonInSingleHead\Print', $schema);
        }
    }
}
