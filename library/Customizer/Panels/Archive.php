<?php

namespace Municipio\Customizer\Panels;

class Archive
{
    public const PANEL_ID = "municipio_customizer_panel_archive";

    public function __construct()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 120,
            'title'       => esc_html__('Archive Apperance', 'municipio'),
            'description' => esc_html__('Manage apperance options on archives.', 'municipio'),
        ));

        //Register panel for each archive
        $archives = $this->getArchives();
        if (is_array($archives) && !empty($archives)) {
            foreach ($archives as $archive) {
                new \Municipio\Customizer\Sections\Archive(
                    self::PANEL_ID,
                    $archive
                );
            }
        }
    }

    /**
     * Fetch archives
     *
     * @return array
     */
    private function getArchives(): array
    {
        $postTypes = array();

        foreach ((array) get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public || $args->name === 'page') {
                continue;
            }

            //Taxonomies
            $args->taxonomies = $this->getTaxonomies($postType);

            //Order By
            $args->orderBy = $this->getOrderBy($postType);

            $postTypes[$postType] = $args;
        }

        $postTypes['author'] = (object) array(
            'name' => 'author',
            'label' => __('Author'),
            'has_archive' => true,
            'is_author_archive' => true
        );

        return $postTypes;
    }

    /**
     * Get taxonomies for post type
     *
     * @param string $postType
     * @return array
     */
    private function getTaxonomies($postType): array
    {
        $taxonomies = get_object_taxonomies($postType, 'objects');

        if (is_array($taxonomies) && !empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                if ($taxonomy->public) {
                    $stack[$taxonomy->name] = $taxonomy->label;
                }
            }

            return $stack;
        }

        return [];
    }

    /**
     * Get order by options for post type
     *
     * @param string $postType
     * @return array
     */
    private function getOrderBy($postType): array
    {
        // Post sorting
        $metaKeys = array(
          'post_date'  => 'Date published',
          'post_modified' => 'Date modified',
          'post_title' => 'Title',
        );

        $metaKeysRaw = \Municipio\Helper\Post::getPosttypeMetaKeys($postType);

        if (isset($metaKeysRaw) && is_array($metaKeysRaw) && !empty($metaKeysRaw)) {
            foreach ($metaKeysRaw as $metaKey) {
                $metaKeys[$metaKey->meta_key] = $metaKey->meta_key;
            }
        }

        return $metaKeys;
    }
}
