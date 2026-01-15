<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use wpdb;

/**
 * Class FilterOutObjectsThatHaveNotChanged
 */
class FilterOutObjectsThatHaveNotChanged
{
    public const VERSION = '1';

    public function __construct(private wpdb $wpdb, private string $postType)
    {
    }

    /**
     * Filters out schema objects that have not changed since last sync.
     *
     * @param BaseType[] $schemaObjects
     * @return BaseType[]
     */
    public function filter(array $schemaObjects): array
    {
        $dbChecksums = $this->fetchChecksumsFromDb();

        // Index schema objects by their @id for quick lookup
        $indexedObjects = [];
        foreach ($schemaObjects as $key => $object) {
            $id = $object->getProperty('@id');
            if ($id !== null) {
                $indexedObjects[$id] = $key;
            }
        }

        foreach ($dbChecksums as $row) {
            $originId = $row->originId;
            if (!isset($indexedObjects[$originId])) {
                continue;
            }

            $key                  = $indexedObjects[$originId];
            $schemaObject         = $this->ensureChecksumProperty($schemaObjects[$key]);
            $schemaObjectChecksum = $this->extractChecksum($schemaObject);

            if ($schemaObjectChecksum === $row->checksum) {
                unset($schemaObjects[$key]);
            } else {
                $schemaObjects[$key] = $schemaObject;
            }
        }

        return $schemaObjects;
    }

    /**
     * Fetches originId and checksum for all posts of the given post type.
     *
     * @return object[]
     */
    private function fetchChecksumsFromDb(): array
    {
        $query = $this->getChecksumQuery();
        return $this->wpdb->get_results($this->wpdb->prepare($query, $this->postType));
    }

    /**
     * Returns the SQL query for fetching checksums.
     *
     * @return string
     */
    private function getChecksumQuery(): string
    {
        $randomNumber = rand(1000, 9999); // To avoid caching issues
        return <<<SQL
            SELECT 
                post_id AS postId,
                MAX(CASE WHEN meta_key = 'originId' THEN meta_value END) AS originId,
                MAX(CASE WHEN meta_key = 'checksum' THEN meta_value END) AS checksum
            FROM {$this->wpdb->prefix}postmeta
            WHERE meta_key IN ('originId', 'checksum')
            AND post_id IN (
                SELECT ID FROM {$this->wpdb->prefix}posts WHERE post_type = %s
            )
            AND {$randomNumber} = {$randomNumber}
            GROUP BY post_id;
        SQL;
    }

    /**
     * Ensures the schema object has a checksum property.
     *
     * @param BaseType $schemaObject
     * @return BaseType
     */
    private function ensureChecksumProperty(BaseType $schemaObject): BaseType
    {
        $meta = $schemaObject->getProperty('@meta');
        if (!is_array($meta)) {
            $meta = [];
        }

        foreach ($meta as $metaProperty) {
            if (
                $metaProperty instanceof BaseType &&
                $metaProperty->getProperty('name') === 'checksum'
            ) {
                return $schemaObject;
            }
        }

        $checkSum          = static::generateChecksum($schemaObject);
        $metaPropertyValue = Schema::propertyValue()
            ->setProperty('name', 'checksum')
            ->setProperty('value', $checkSum);

        $schemaObject->setProperty('@meta', [...$meta, $metaPropertyValue]);
        return $schemaObject;
    }

    public static function generateChecksum(BaseType $schemaObject): string
    {
        return md5(json_encode($schemaObject)) . self::VERSION;
    }

    /**
     * Extracts the checksum value from the schema object's @meta property.
     *
     * @param BaseType $schemaObject
     * @return string|null
     */
    private function extractChecksum(BaseType $schemaObject): ?string
    {
        $meta = $schemaObject->getProperty('@meta');
        if (!is_array($meta)) {
            return null;
        }

        foreach ($meta as $metaProperty) {
            if (
                $metaProperty instanceof BaseType &&
                $metaProperty->getProperty('name') === 'checksum'
            ) {
                return $metaProperty->getProperty('value');
            }
        }

        return null;
    }
}
