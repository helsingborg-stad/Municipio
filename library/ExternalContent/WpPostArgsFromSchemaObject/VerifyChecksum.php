<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\BaseType;
use WpService\Contracts\GetPostMeta;

/**
 * Class VerifyChecksum
 *
 * Set the post arguments ID to something that will prevent from being updated or created if the checksum is the same.
 */
class VerifyChecksum implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * WpPostMetaFactoryVersionDecorator constructor.
     *
     * @param WpPostMetaFactoryInterface $inner
     * @param GetPostMeta $wpService
     */
    public function __construct(
        private WpPostArgsFromSchemaObjectInterface $inner,
        private GetPostMeta $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        $postArgs = $this->inner->transform($schemaObject);

        if (!isset($postArgs['meta_input']['checksum']) || !isset($postArgs['ID'])) {
            return $postArgs;
        }

        // Check $postArgs['meta_input']['checksum'] against previous checksum in database
        // If they are the same, set ID to -1 to prevent update
        // If they are different, return $postArgs as is
        $previousChecksum = $this->wpService->getPostMeta($postArgs['ID'], 'checksum', true);

        if ($previousChecksum === $postArgs['meta_input']['checksum']) {
            $postArgs['ID'] = -1;
        }

        return $postArgs;
    }
}
