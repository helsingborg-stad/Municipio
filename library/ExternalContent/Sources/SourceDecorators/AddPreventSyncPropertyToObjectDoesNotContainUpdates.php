<?php

namespace Municipio\ExternalContent\Sources\SourceDecorators;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Query;
use wpdb;
use WpService\Contracts\GetPosts;

class AddPreventSyncPropertyToObjectDoesNotContainUpdates implements SourceInterface
{
    public function __construct(private GetPosts $wpService, private wpdb $wpdb, private SourceInterface $inner)
    {
    }

    public function getId(): string
    {
        return $this->inner->getId();
    }

    public function getObject(string|int $id): null|BaseType
    {
        return $this->inner->getObject($id);
    }

    public function getObjects(?WP_Query $query = null): array
    {
        $objects   = $this->inner->getObjects($query);
        $dbResults = $this->wpdb->get_results(
            "SELECT 
                    meta_origin.meta_value AS originId,
                    meta_version.meta_value AS version
            FROM {$this->wpdb->posts} p
            JOIN {$this->wpdb->postmeta} meta_origin 
                ON p.ID = meta_origin.post_id 
                AND meta_origin.meta_key = 'originId'
            JOIN {$this->wpdb->postmeta} meta_version 
                ON p.ID = meta_version.post_id 
                AND meta_version.meta_key = 'version'
            WHERE p.post_type = '{$this->getPostType()}'",
            OBJECT_K
        );

        $objects = array_map(function ($object) use ($dbResults) {
            $version  = $object->getProperty('@version');
            $originId = $object->getProperty('@id');

            if (empty($version) || empty($originId)) {
                return $object;
            }

            if (!isset($dbResults[$originId])) {
                return $object;
            }

            if ($dbResults[$originId]->version === $version) {
                $object->setProperty('@preventSync', true);
            }

            return $object;
        }, $objects);

        return $objects;
    }

    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }

    public function getSchemaObjectType(): string
    {
        return $this->inner->getSchemaObjectType();
    }
}
