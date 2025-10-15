<?php

namespace Modularity\Module\Table;

class Table extends \Modularity\Module
{
    public $slug = 'table';
    public $supports = array();

    public function init()
    {
        $this->nameSingular = __("Table", 'modularity');
        $this->namePlural = __("Tables", 'modularity');
        $this->description = __("Outputs a flexible table with options.", 'modularity');

        add_action('Modularity/Module/mod-table/enqueue', array($this, 'modAssets'));
        add_action('save_post', array($this, 'csvImport'), 999);

        //Remove html filter
        add_action('save_post', array($this, 'disableHTMLFiltering'), 5);
    }

    /**
     * Removes the filter of html & script data before save.
     * @var int
     */
    public function disableHTMLFiltering($postId)
    {
        //Bail early if not a script module save
        if (get_post_type($postId) !== "mod-" . $this->slug) {
            return;
        }

        //Disable filter temporarily
        add_filter('acf/allow_unfiltered_html', function ($allow_unfiltered_html) {
            return true;
        });
    }

    public function data(): array
    {
        $post = $this->data;
        $data = (array) $this->getFields();

        $tableList = $this->tableList($this->getTableData($post, $data));

        $data['mod_table_size'] = $data['mod_table_size'] ?? '';
        $data['m_table']        = [
            'data'              => (array) $tableList,
            'showHeader'        => true,    //To-Do: Add this option in ACF
            'showFooter'        => false,   //To-Do: Add this option in ACF
            'classList'         => !empty($this->getTableClasses($data)) ? $this->getTableClasses($data) : [],
            'filterable'        => !empty($data['mod_table_search']) ? $data['mod_table_search'] : [],
            'sortable'          => !empty($data['mod_table_ordering']) ? $data['mod_table_ordering'] : [],
            'pagination'        => !empty($data['mod_table_pagination']) ? $data['mod_table_pagination_count'] : false,
            'multidimensional'  => !empty($data['mod_table_multidimensional']) ? $data['mod_table_multidimensional'] : false,
            'showSum'           => !empty($data['mod_table_sum']) ? $data['mod_table_sum'] : false,
            'fullscreen'        => !empty($data['mod_table_fullscreen']) ? $data['mod_table_fullscreen'] : false
        ];

        $data['mod_table']      = self::unicodeConvert($data['mod_table']);
        $data['tableClasses']   = $this->getTableClasses($data);
        $data['classes']        = implode(
            ' ', 
            apply_filters(
                'Modularity/Module/Classes', 
                array('c-card--default'), 
                isset($this->post_type) ? $this->post_type : false, 
                $this->args
            )
        );
        $data['m_table']        = (object) $data['m_table'];
        $data['id'] = $this->ID;

        return $data;
    }

    public static function unicodeConvert($unicode)
    {
        $search = array('u00a5', 'u201e', 'u00b6', 'u2026', 'u00a4', 'u2013', 'u0152', 'u0160', 'u0161', 'ufffe', 'u20ac', 'u2026', 'u2020', '	u201e', 'u201d', 'ufffe', 'u017d', 'u2122', 'u00c3', 'u00e2', 'u00e2u201au00ac', 'u00a9', 'u2030', 'u00c2u00b4', 'Äu02dc', 'u00db');
        $replace = array('å', 'ä', 'ö', 'Å', 'Ä', 'Ö', 'å', 'ä', 'ö', 'Å', 'Ä', 'Ö', 'å', 'ä', 'ö', 'Å', 'Ä', 'Ö', '', '”', '€', 'é', 'É', '´', "'", '€');

        return str_replace($search, $replace, $unicode);
    }

    public function getTableClasses($data)
    {
        $classes = '';

        if (isset($data['mod_table_classes']) && is_array($data['mod_table_classes'])) {
            $classes = $data['mod_table_classes'];

            if (isset($data['mod_table_size']) && !empty($data['mod_table_size'])) {
                $classes[] = $data['mod_table_size'];
            }

            $classes = array_unique($classes);
            $classes = implode(' ', $classes);
        }

        return $classes;
    }

    private function getTableData($post, $data)
    {
        if (!empty($data['mod_table_block_csv_file'])) {
            $tableData = $this->formatCsvData($data['mod_table_block_csv_file'], $data['mod_table_csv_delimiter']);
        } elseif (isset($data['mod_table_csv_file']) && $data['mod_table_csv_file'] && !empty($data['mod_table_data_type']) && $data['mod_table_data_type'] === 'csv') {
            $tableData = $this->formatCsvData($data['mod_table_csv_file'], $data['mod_table_csv_delimiter']);
        } elseif (isset($post['meta']['mod_table'][0]) && !empty(json_decode($post['meta']['mod_table'][0]))) {
            $tableData = json_decode($post['meta']['mod_table'][0]);
        } else {
            $tableData = $data['mod_table'];
        }

        return $tableData;
    }

    private function formatCsvData($file, $delimiter) {
        $file = fopen($file['url'], 'r');

        $data = array();

        if (!$file) {
            wp_die(__('There was an error opening the selected .csv-file.'));
        }

        while (!feof($file)) {
            $row = fgetcsv($file, 0, $delimiter);

            if (!is_array($row) || count($row) === 0) {
                continue;
            }

            array_push($data, $row);
        }

        fclose($file);

        return $data;
    }

    public function csvImport($post_id)
    {
        if (!isset($_POST['post_type']) || $_POST['post_type'] != $this->moduleSlug) {
            return;
        }

        if (get_field('mod_table_data_type', $post_id) != 'csv') {
            return;
        }

        ini_set('auto_detect_line_endings', true);


        $file = get_field('mod_table_csv_file', $post_id);
        $file = fopen($file['url'], 'r');

        $data = array();

        if (!$file) {
            wp_die(__('There was an error opening the selected .csv-file.'));
        }

        while (!feof($file)) {
            $row = fgetcsv($file, 0, ';');

            if (!is_array($row) || count($row) === 0) {
                continue;
            }

            foreach ($row as &$value) {
                $value = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value));
            }

            array_push($data, $row);
        }

        fclose($file);

        $data = array_filter($data);
        $data = json_encode($data);

        update_post_meta($post_id, 'mod_table', $data);
        update_post_meta($post_id, '_mod_table', 'field_5666a2ae23643');
    }

    public function modAssets()
    {
        wp_register_style('mod-table', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('css/table.css'));

        wp_enqueue_style('mod-table');
    }

    public function tableList($arr)
    {
        $data = [];
        if (!is_array($arr)) {
            return $data;
        }

        if (array_key_exists('header', $arr)) { 
           if (!empty($arr['header'])) {
                foreach ($arr['header'] as $heading) {
                    $data['headings'][] = $heading['c']; 
                }
            } else {
                $data['headings'] = []; 
            }
            foreach ($arr['body'] as $row) {
                $columns = [];
                foreach ($row as $column) {
                    $columns[] = $column['c'];
                }
                $data['list'][]['columns'] = $columns;
            }
            return $data;
        }

        foreach ($arr as $row => $cols) {
            if ($row !== 0) {
                $data['list'][]['columns'] = array_values($arr[$row]);
            } else {
                $data['headings'] = array_values($arr[$row]);
            }
        }

        return $data;
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
