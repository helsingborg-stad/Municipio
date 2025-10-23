<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync;

use Municipio\SchemaData\ExternalContent\SyncHandler\SyncHandler;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use wpdb;

/**
 * Class FilterOutObjectsThatHaveNotChanged
 */
class FilterOutObjectsThatHaveNotChanged implements Hookable
{
    public function __construct(private wpdb $wpdb, private string $postType)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        add_filter(SyncHandler::FILTER_BEFORE, [$this, 'filter']);
    }

    /**
     * Filter out duplicate objects from a collection based on their unique identifier.
     *
     * @param BaseType[] $schemaObjects
     */
    public function filter(array $schemaObjects): array
    {
        $query = <<<SQL
            SELECT 
                post_id AS postId,
                MAX(CASE WHEN meta_key = 'originId' THEN meta_value END) AS originId,
                MAX(CASE WHEN meta_key = 'checksum' THEN meta_value END) AS checksum
            FROM {$this->wpdb->prefix}postmeta
            WHERE meta_key IN ('originId', 'checksum')
                AND post_id IN (
                    SELECT ID FROM {$this->wpdb->prefix}posts WHERE post_type = %s
                )
            GROUP BY post_id;
        SQL;

        $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->postType));

        foreach ($result as $row) {
            foreach ($schemaObjects as $key => &$schemaObject) {
                if ($schemaObject->getProperty('@id') === $row->originId) {
                    $schemaObject         = $this->setSchemaObjectChecksum($schemaObject);
                    $schemaObjectChecksum = $this->getSchemaObjectChecksum($schemaObject);
                    if ($schemaObjectChecksum === $row->checksum) {
                        unset($schemaObjects[$key]);
                    }
                }
            }
        }

        return $schemaObjects;
    }

    /**
     * Set checksum property on schema object if not already set.
     */
    private function setSchemaObjectChecksum(BaseType $schemaObject): BaseType
    {
        if (!is_array($schemaObject->getProperty('@meta'))) {
            $schemaObject->setProperty('@meta', []);
        }

        $meta = $schemaObject->getProperty('@meta');

        if (!array_key_exists('checksum', $meta)) {
            $metaPropertyValue = Schema::propertyValue()
                ->setProperty('name', 'checksum')
                ->setProperty('value', md5(json_encode($schemaObject)));

            $schemaObject->setProperty('@meta', [...$meta, $metaPropertyValue]);
        }

        return $schemaObject;
    }

    private function getSchemaObjectChecksum(BaseType $schemaObject): ?string
    {
        $meta = $schemaObject->getProperty('@meta');

        if (!is_array($meta)) {
            return null;
        }

        foreach ($meta as $metaProperty) {
            if (
                $metaProperty instanceof BaseType
                && $metaProperty->getProperty('name') === 'checksum'
            ) {
                return $metaProperty->getProperty('value');
            }
        }

        return null;
    }
}
