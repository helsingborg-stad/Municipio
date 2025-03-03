<?php

namespace Municipio\ExternalContent\SyncHandler;

use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use WpService\Contracts\WpInsertPost;

/**
 * Class SyncHandler
 */
class SyncHandler implements SyncHandlerInterface
{
    /**
     * Constructor for the SyncHandler class.
     *
     * @param SourceReaderInterface $sourceReader
     * @param WpPostArgsFromSchemaObjectInterface $wpPostFactory
     * @param WpInsertPost $wpService
     */
    public function __construct(
        private SourceReaderInterface $sourceReader,
        private WpPostArgsFromSchemaObjectInterface $wpPostArgsFromSchemaObject,
        private WpInsertPost $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function sync(): void
    {
        $schemaObjects   = $this->sourceReader->getSourceData();
        $wpPostArgsArray = array_map(fn($schemaObject) => $this->wpPostArgsFromSchemaObject->transform($schemaObject), $schemaObjects);

        foreach ($wpPostArgsArray as $postArgs) {
            $this->wpService->wpInsertPost($postArgs);
        }
    }
}
