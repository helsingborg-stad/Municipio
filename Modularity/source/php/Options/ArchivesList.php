<?php

declare(strict_types=1);

namespace Modularity\Options;

class ArchivesList extends \WP_List_Table
{
    public function __construct()
    {
        parent::__construct([
            'singular' => __('Archive', 'municipio'),
            'plural' => __('Archives', 'municipio'),
            'ajax' => false,
        ]);
    }

    public function no_items()
    {
        echo __('There\'s no archives to display', 'municipio');
    }

    public function get_columns()
    {
        return [
            'archive' => __('Post Type Archive', 'municipio'),
            'has_modules' => __('Has modules', 'municipio'),
        ];
    }

    public function prepare_items()
    {
        $items = \Modularity\Options\Archives::getArchives();

        // Columns
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];

        // Pagination
        $this->set_pagination_args([
            'total_items' => count($items),
            'per_page' => 10,
            'total_pages' => ceil(count($items) / 10),
        ]);

        // Items
        $this->items = $items;
    }

    public function column_default($item, $columnName)
    {
    }

    public function column_archive($item)
    {
        $editorLink = admin_url('options.php?page=modularity-editor&id=archive-' . $item->rewrite['slug']);

        $actions = [
            'view' => sprintf(
                '<a href="%s" target="_blank">' . __('View') . '</a>',
                get_post_type_archive_link($item->rewrite['slug']),
            ),
            'edit' => sprintf('<a href="' . $editorLink . '">' . __('Edit modules', 'municipio') . '</a>'),
        ];

        return sprintf(
            '%1$s %2$s',
            '<a href="' . $editorLink . '" class="row-title">' . $item->labels->name . '</a>',
            $this->row_actions($actions),
        );
    }
}
