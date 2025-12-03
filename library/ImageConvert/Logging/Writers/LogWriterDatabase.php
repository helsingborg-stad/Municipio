<?php

namespace Municipio\ImageConvert\Logging\Writers;

use Municipio\ImageConvert\Logging\LogEntry;
use Municipio\ImageConvert\Logging\Writers\LogWriterInterface;

class LogWriterDatabase implements LogWriterInterface
{
    private \wpdb $wpdb;
    private string $table;
    private static bool $checkedTable = false;

    public function __construct(string $table = 'imageconvert_log')
    {
        global $wpdb;
        $this->wpdb  = $wpdb;
        $this->table = $this->wpdb->prefix . $table;

        if (!self::$checkedTable) {
            $this->maybeCreateTable();
            self::$checkedTable = true;
        }
    }

    public function write(string $formatted, LogEntry $entry): void
    {
        $this->wpdb->insert(
            $this->table,
            [
                'level'      => $entry->getLevel()->value,
                'context'    => get_class($entry->getContext()),
                'message'    => $entry->getMessage(),
                'metadata'   => maybe_serialize($entry->getMetadata()),
                'formatted'  => $formatted,
                'created_at' => current_time('mysql', true),
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ]
        );
    }

    private function maybeCreateTable(): void
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charsetCollate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            level VARCHAR(20) NOT NULL,
            context VARCHAR(191) NOT NULL,
            message TEXT NOT NULL,
            metadata LONGTEXT NULL,
            formatted LONGTEXT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY level (level),
            KEY context (context),
            KEY created_at (created_at)
        ) $charsetCollate;";

        dbDelta($sql);
    }
}
