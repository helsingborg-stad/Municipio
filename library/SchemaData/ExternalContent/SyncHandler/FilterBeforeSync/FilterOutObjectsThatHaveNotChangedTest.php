<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\Filter;
use wpdb;

class FilterOutObjectsThatHaveNotChangedTest extends TestCase
{
    #[TestDox('Filter out objects that have not changed')]
    public function testFilterOutUnchangedObjects()
    {
        $schemaObject = Schema::thing()->setProperty('@id', 'originId1')->setProperty('@meta', []);
        $expectedChecksum = FilterOutObjectsThatHaveNotChanged::generateChecksum($schemaObject);
        $schemaObjects = [$schemaObject];
        $wpdb = new class($expectedChecksum) extends wpdb {
            private $expectedChecksum;

            public function __construct($expectedChecksum)
            {
                parent::__construct('', '', '', '');
                $this->expectedChecksum = $expectedChecksum;
            }

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_results($query = \null, $output = \OBJECT)
            {
                return [
                    (object) [
                        'postId' => 1,
                        'originId' => 'originId1',
                        'checksum' => $this->expectedChecksum,
                    ],
                ];
            }
        };

        $filter = new FilterOutObjectsThatHaveNotChanged($wpdb, 'custom_post_type');
        $filtered = $filter->filter($schemaObjects);

        $this->assertCount(0, $filtered, 'Expected no schema objects after filtering out unchanged objects.');
    }

    #[TestDox('Do not filter out objects that have changed')]
    public function testDoNotFilterChangedObjects()
    {
        $schemaObject = Schema::thing()->setProperty('@id', 'originId1')->setProperty('@meta', []);
        $expectedChecksum = md5(json_encode($schemaObject)) . 'modified';
        $schemaObjects = [$schemaObject];
        $wpdb = new class($expectedChecksum) extends wpdb {
            private $expectedChecksum;

            public function __construct($expectedChecksum)
            {
                parent::__construct('', '', '', '');
                $this->expectedChecksum = $expectedChecksum;
            }

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_results($query = \null, $output = \OBJECT)
            {
                return [
                    (object) [
                        'postId' => 1,
                        'originId' => 'originId1',
                        'checksum' => $this->expectedChecksum,
                    ],
                ];
            }
        };

        $filter = new FilterOutObjectsThatHaveNotChanged($wpdb, 'custom_post_type');
        $filtered = $filter->filter($schemaObjects);

        $this->assertCount(1, $filtered, 'Expected schema objects to remain after filtering changed objects.');
    }
}
