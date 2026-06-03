<?php

declare(strict_types=1);


namespace Municipio\SchemaData\ExternalContent\SourceReaders;

use RuntimeException;
use WpService\Contracts\__;

class TrustworthySourceReader implements SourceReaderInterface
{
    public function __construct(
        private SourceReaderInterface $sourceReader,
        private __ $wpService,
        private int $maxNumberOfCalls = 3, // Number of subsequent calls to the inner source reader to verify the consistency of the result
        private int $subSequentCallDelayInMs = 1000, // Delay in milliseconds between subsequent calls to the inner source reader to allow for any transient inconsistencies to resolve
        private Logger\LoggerInterface $logger = new Logger\Logger()
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getSourceData(): array
    {
        $result = $this->sourceReader->getSourceData();
        $check = null;

        for ($i = 0; $i < $this->maxNumberOfCalls; $i++) {
            usleep($this->subSequentCallDelayInMs * 1000);
            $check = $this->sourceReader->getSourceData();

            if( json_encode($result) === json_encode($check)) {
                return $check;
            } else {
                $result = $check;
            }

        }

        $resultCount = count($result);
        $checkCount = count($check);
        $this->logger->logError("TrustworthySourceReader: Result count: {$resultCount}, Check count: {$checkCount}");
        throw new RuntimeException($this->wpService->__('We could not verify the external content at this time. Please try again later.', 'municipio'));
    }
}
