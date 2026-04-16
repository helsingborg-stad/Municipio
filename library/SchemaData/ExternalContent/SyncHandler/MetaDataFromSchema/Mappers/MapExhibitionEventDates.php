<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema\Mappers;

use Municipio\Schema\BaseType;
use Municipio\Schema\Contracts\ExhibitionEventContract;
use Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema\MetaDataItemInterface;

/**
 * Maps startDate and endDate from ExhibitionEvent schema objects to meta data items.
 * ExhibitionEvent stores these as direct schema properties, unlike Event which uses eventSchedule.
 */
class MapExhibitionEventDates implements MetaDataItemMapperInterface
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @inheritDoc
     */
    public function map(BaseType $schema): \Generator
    {
        if (!$schema instanceof ExhibitionEventContract) {
            return;
        }

        foreach (['startDate', 'endDate'] as $property) {
            $date = $schema->getProperty($property);
            $formatted = $this->formatDate($date);

            if ($formatted !== null) {
                yield $this->createMetaDataItem($property, $formatted);
            }
        }
    }

    /**
     * Format a date value to MySQL DATETIME string.
     */
    private function formatDate(mixed $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        if ($date instanceof \DateTimeInterface) {
            return $date->format(self::DATE_FORMAT);
        }

        if (is_string($date)) {
            $time = strtotime($date);
            if ($time !== false) {
                return date(self::DATE_FORMAT, $time);
            }
        }

        return null;
    }

    /**
     * Create a MetaDataItem from a property key and formatted date value.
     */
    private function createMetaDataItem(string $key, string $value): MetaDataItemInterface
    {
        return new class($key, $value) implements MetaDataItemInterface {
            public function __construct(
                private string $key,
                private string $value,
            ) {}

            public function getKey(): string
            {
                return $this->key;
            }

            public function getValue(): mixed
            {
                return $this->value;
            }
        };
    }
}
