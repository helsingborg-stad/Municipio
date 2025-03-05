<?php

namespace Municipio\ExternalContent\SyncHandler;

use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Spatie\SchemaOrg\BaseType;
use WpService\Contracts\{WpInsertPost, DoAction, DoActionRefArray};

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
     * @param WpInsertPost&DoAction&DoActionRefArray $wpService
     */
    public function __construct(
        private SourceReaderInterface $sourceReader,
        private WpPostArgsFromSchemaObjectInterface $wpPostArgsFromSchemaObject,
        private WpInsertPost&DoActionRefArray $wpService
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
        $this->wpService->doActionRefArray(self::ACTION_AFTER, [$schemaObjects]);
    }
}
