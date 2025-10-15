<?php

namespace Modularity\Upgrade\Version;

use \Modularity\Upgrade\Migrators\Block\AcfBlockMigration;
use \Modularity\Upgrade\Migrators\Module\AcfModuleMigration;
use \Modularity\Upgrade\Version\Helper\GetPostsByPostType;

class V5 implements versionInterface {
    private $db;
    private $oldName;
    private $newName;

    public function __construct(\wpdb $db) {
        $this->db = $db;
        $this->oldName = 'posts';
        $this->newName = 'manualinput';
    }

    public function upgrade(): bool
    {
        $this->upgradeBlocks();
        $this->upgradeModules();

        return true;
    }

    private function upgradeModules() 
    {
        $moduleMigrator = new AcfModuleMigration(
            $this->db,
            $this->getModules(),
            $this->getModuleFields(),
            'mod-' . $this->newName
        );

        $moduleMigrator->migrateModules();
    }

    private function upgradeBlocks() 
    {
        $blockMigrator = new AcfBlockMigration(
            $this->db,
            'acf/' . $this->oldName,
            $this->getBlockFields(),
            'acf/' . $this->newName,
            array($this, 'blockConditionCallback')
        );

        $blockMigrator->migrateBlocks();
    }

    public function blockConditionCallback($block) {
        return !empty($block['attrs']['data']['posts_data_source']) && $block['attrs']['data']['posts_data_source'] == 'input';
    }

    private function getModules():array 
    {
        $postsModules = GetPostsByPostType::getPostsByPostType('mod-' . $this->oldName);

        $filteredPostsModules = array_filter($postsModules, function ($module) {
            if (!empty($module->ID)) {
                $source = get_field('posts_data_source', $module->ID);
                return !empty($source) && $source == 'input';
            }
            return false;
        });

        return $filteredPostsModules ?? [];
    }

    private function getModuleFields():array
    {
        return [
            'post_title'    => 'title',
            'post_content'  => 'content',
            'data'          => [
                'name'      => 'manual_inputs', 
                'type'      => 'repeater', 
                'fields'    => [
                    'post_title'    => 'title', 
                    'post_content'  => 'content',
                    'column_values' => 'accordion_column_values',
                    'permalink'     => 'link',
                    'item_icon'     => 'box_icon'
                ]
            ],
            'posts_columns' => [
                'name'      => 'columns',
                'type'      => 'replaceValue',
                'values'    => [
                    'grid-md-12'    => 'o-grid-12',
                    'grid-md-6'     => 'o-grid-6',
                    'grid-md-4'     => 'o-grid-4',
                    'grid-md-3'     => 'o-grid-3',
                    'default'       => 'o-grid-4'
                ]
            ],
            'posts_display_as' => [
                'name'      => 'display_as', 
                'type'      => 'replaceValue', 
                'values'    => [
                    'list'              => 'list', 
                    'expandable-list'   => 'accordion', 
                    'items'             => 'card', 
                    'news'              => 'card', 
                    'index'             => 'card', 
                    'segment'           => 'segment', 
                    'collection'        => 'collection', 
                    'features-grid'     => 'box', 
                    'grid'              => 'block', 
                    'default'           => 'card'
                ]
            ],
            'posts_list_column_titles' => [
                'name'      => 'accordion_column_titles',
                'type'      => 'repeater',
                'fields'    => [
                    'column_header' => 'accordion_column_title', 
                ]
            ],
            'title_column_label' => 'accordion_column_marking',
            'posts_highlight_first' => 'highlight_first_input',
            
        ];
    }

    private function getBlockFields():array 
    {
        return [
            'posts_display_as' => [
                'name'      => 'display_as', 
                'key'       => 'field_64ff23d0d91bf',
                'type'      => 'replaceValue', 
                'values'    => [
                    'list'              => 'list', 
                    'expandable-list'   => 'accordion', 
                    'items'             => 'card', 
                    'news'              => 'card', 
                    'index'             => 'card', 
                    'segment'           => 'segment', 
                    'collection'        => 'collection', 
                    'features-grid'     => 'box', 
                    'grid'              => 'block', 
                    'default'           => 'card'
                ],  
            ],
            'posts_columns' => [
                'name'      => 'columns',
                'key'       => 'field_65001d039d4c4',
                'type'      => 'replaceValue',
                'values'    => [
                    'grid-md-12'    => 'o-grid-12',
                    'grid-md-6'     => 'o-grid-6',
                    'grid-md-4'     => 'o-grid-4',
                    'grid-md-3'     => 'o-grid-3',
                    'default'       => 'o-grid-4'
                ]
            ],
            'data' => [
                'name'      => 'manual_inputs', 
                'key'       => 'field_64ff22b2d91b7',
                'type'      => 'repeater', 
                'fields'    => [
                    'post_title'    => [
                        'name'  => 'title', 
                        'key'   => 'field_64ff22fdd91b8'
                    ], 
                    'post_content' => [
                        'name'  => 'content', 
                        'key'   => 'field_64ff231ed91b9'
                    ],
                    'permalink' => [
                        'name'  => 'link', 
                        'key'   => 'field_64ff232ad91ba'
                    ],
                    'item_icon' => [
                        'name'  => 'box_icon', 
                        'key'   => 'field_65293de2a26c7'
                    ],
                    'image' => [
                        'name'  => 'image', 
                        'key'   => 'field_64ff2355d91bb'
                    ],
                    'column_values' => [
                        'name'      => 'accordion_column_values', 
                        'key'       => 'field_64ff2372d91bc', 
                        'type'      => 'repeater', 
                        'fields'    => [
                            'value' => [
                                'name'  => 'value', 
                                'key'   => 'field_64ff23afd91bd'
                            ]
                        ]
                    ]
                ]
            ],
            'posts_list_column_titles' => [
                'name'      => 'accordion_column_titles',
                'key'       => 'field_65005968bbc75',
                'type'      => 'repeater',
                'fields'    => [
                    'column_header' => [
                        'name'  => 'accordion_column_title', 
                        'key'   => 'field_65005a33bbc77'
                    ], 
                ]
            ],
            'title_column_label' => [
                'name' => 'accordion_column_marking', 
                'key' => 'field_650067ed6cc3c'
            ],
            'image_position' => [
                'name' => 'image_position', 
                'key' => 'field_6641de045ab9d'
            ],
            'posts_highlight_first' => [
                'name' => 'highlight_first_input', 
                'key' => 'field_663372f4922a5'
            ],
        ];
    }

}