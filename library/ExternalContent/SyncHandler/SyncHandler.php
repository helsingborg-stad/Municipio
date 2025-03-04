<?php

namespace Municipio\ExternalContent\SyncHandler;

use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\{WpInsertPost, DoAction};

/**
 * Class SyncHandler
 */
class SyncHandler implements SyncHandlerInterface
{
    public const ACTION_AFTER = 'Municipio/ExternalContent/Sync/After';

    /**
     * Constructor for the SyncHandler class.
     *
     * @param SourceReaderInterface $sourceReader
     * @param WpPostArgsFromSchemaObjectInterface $wpPostFactory
     * @param WpInsertPost&DoAction $wpService
     */
    public function __construct(
        private SourceReaderInterface $sourceReader,
        private WpPostArgsFromSchemaObjectInterface $wpPostArgsFromSchemaObject,
        private WpInsertPost&DoAction $wpService
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

        /**
         * Action after sync.
         *
         * @param BaseType[] $schemaObjects
         */
        $this->wpService->doAction(self::ACTION_AFTER, $schemaObjects);
    }
}
