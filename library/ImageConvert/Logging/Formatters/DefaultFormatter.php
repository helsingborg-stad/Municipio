<?php

namespace Municipio\ImageConvert\Logging\Formatters;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\Logging\LogEntry;

class DefaultFormatter implements LogFormatterInterface
{
    public function formatEntry(LogEntry $entry): string
    {
        $contextClass = $entry->getContext() ? get_class($entry->getContext()) : 'unknown';
        $metadata     = $entry->getMetadata();

        $imagePart = '';
        if (isset($metadata['image']) && $metadata['image'] instanceof ImageContract) {
            $image     = $metadata['image'];
            $imagePart = sprintf(
                ' | Image ID: %s | Dimensions: %s x %s',
                $image->getId(),
                $image->getWidth(),
                $image->getHeight()
            );
            unset($metadata['image']);
        }

        $metadataStr = !empty($metadata) ? json_encode($metadata) : '[]';

        $page = $_SERVER['REQUEST_URI'] ?? 'CLI or unknown';
        $page = str_replace(["\n", "\r"], ['%0A', '%0D'], $page);
        $page = preg_replace('/(token|password)=([^&]+)/i', '$1=***', $page);

        return sprintf(
            '[%s] %s | Context: %s | Page: %s%s | Metadata: %s | Message: %s',
            strtoupper($entry->getLevel()->value),
            date('Y-m-d H:i:s'),
            $contextClass,
            $page,
            $imagePart,
            $metadataStr,
            $entry->getMessage()
        );
    }
}
