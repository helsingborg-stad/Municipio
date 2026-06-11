<?php

declare(strict_types=1);

namespace Modularity\Module\Posts\TemplateController;

/**
 * Class ExpandableListTemplate
 *
 * Template controller for rendering posts as an expandable list.
 *
 * @package Modularity\Module\Posts\TemplateController
 */
class ExpandableListTemplate extends AbstractController
{
    /**
     * The instance of the Posts module associated with this template.
     *
     * @var \Modularity\Module\Posts\Posts
     */
    protected $module;

    /**
     * The arguments passed to the template controller.
     *
     * @var array
     */
    protected $args;

    /**
     * Data to be used in rendering the template.
     *
     * @var array
     */
    public $data = [];

    /**
     * Fields ACF fields
     *
     * @var array
     */
    public $fields = [];

    /**
     * ExpandableListTemplate constructor.
     *
     * @param \Modularity\Module\Posts\Posts $module Instance of the Posts module.
     */
    public function __construct(\Modularity\Module\Posts\Posts $module)
    {
        parent::__construct($module);
        $this->module = $module;
        $this->args = $module->args;
        $this->data = $module->data;
        $this->fields = $module->fields;

        $this->data['accordionHeadings'] = $this->getAccordionHeadings();

        $this->data['posts_hide_title_column'] = $this->fields['posts_hide_title_column'] ? true : false;
        $this->data['title_column_label'] = $this->fields['title_column_label'] ?? null;
        $this->data['allow_freetext_filtering'] = $this->fields['allow_freetext_filtering'] ?? null;
        $this->data['prepareAccordion'] = $this->prepareExpandableList();
    }

    private function getTitleColumnLabel(): string
    {
        return $this->fields['title_column_label'] ?? __('Title', 'modularity');
    }

    private function getAccordionHeadings(): array
    {
        $headings = [];

        if (empty($this->fields['posts_hide_title_column'])) {
            $headings[] = $this->getTitleColumnLabel();
        }

        if (!empty($this->fields['posts_list_column_titles']) && is_array($this->fields['posts_list_column_titles'])) {
            foreach ($this->fields['posts_list_column_titles'] as $column) {
                if (empty($column['column_header'])) {
                    continue;
                }

                $headings[] = $column['column_header'];
            }
        }

        return $headings;
    }

    /**
     * Get correct column values
     * @return array An array of column values for a column
     */
    public function getColumnValues(): array
    {
        if (empty($this->data['accordionHeadings'])) {
            return [];
        }

        $columnValues = [];

        foreach ($this->data['posts'] as $colIndex => $post) {
            $columnValues[] = get_post_meta($post->getId(), 'modularity-mod-posts-expandable-list', true) ?? '';
        }

        return $columnValues;
    }

    /**
     * Rewrite post content headings to fit into accordion structure
     *
     * @param array $preparedPosts Array of prepared posts
     *
     * @return array
     */
    private function rewritePostContent($preparedPosts): array
    {
        $increment = 2;

        foreach ($preparedPosts as &$post) {
            for ($i = 5; $i >= 1; $i--) {
                $newLevel = $i + $increment;

                if ($newLevel > 6) {
                    $newLevel = 6;
                }

                $post->postContentFiltered = preg_replace(
                    [
                        '/<h' . $i . '([^>]*)>/i',
                        '/<\/h' . $i . '>/i',
                    ],
                    [
                        '<h' . $newLevel . '$1>',
                        '</h' . $newLevel . '>',
                    ],
                    $post->postContentFiltered,
                );
            }
        }

        return $preparedPosts;
    }

    private function shouldUsePostTitleForHeading(string $title): bool
    {
        return empty($this->fields['posts_hide_title_column']) && $title === $this->getTitleColumnLabel();
    }

    /**
     * Prepare Data for accordion
     * @param array $items Array of posts
     * @param array $this->data Array of settings
     *
     * @return array|null
     */
    public function prepareExpandableList(): null|array
    {
        $accordion = [];

        $this->data['posts'] = $this->rewritePostContent($this->preparePosts($this->module));
        $columnValues = $this->getColumnValues();

        if (!empty($this->data['posts']) && is_array($this->data['posts'])) {
            foreach ($this->data['posts'] as $index => $item) {
                if ($this->hasColumnValues($columnValues) && $this->hasColumnTitles()) {
                    foreach ($this->data['accordionHeadings'] as $colIndex => $title) {
                        $usePostTitleInHeading = $this->shouldUsePostTitleForHeading($title);
                        $sanitizedTitle = sanitize_title($title);
                        if ($this->arrayDepth($columnValues) > 1) {
                            $accordion[$index]['headings'][$colIndex] = $usePostTitleInHeading ? $item->getTitle() : $columnValues[$index][$sanitizedTitle] ?? '';
                        } else {
                            $accordion[$index]['headings'][$colIndex] = $usePostTitleInHeading ? $item->getTitle() : $columnValues[$sanitizedTitle] ?? '';
                        }
                    }
                }

                $accordion[$index]['heading'] = $item->getTitle() ?? '';
                $accordion[$index]['content'] = $item->postContentFiltered ?? '';
                $accordion[$index]['classList'] = $item->classList ?? [];
                $accordion[$index]['attributeList'] = ['data-js-item-id' => $item->getId()];
            }
        }

        if (empty($accordion)) {
            return null;
        }

        return $accordion;
    }

    /**
     * Get array dimension depth
     *
     * @param array $colArray
     *
     * @return int
     */
    public function arrayDepth(array $colArray): int
    {
        $maxDepth = 1;
        foreach ($colArray as $value) {
            if (!is_array($value)) {
                continue;
            }

            $depth = $this->arrayDepth($value) + 1;
            $maxDepth = $depth > $maxDepth ? $depth : $maxDepth;
        }

        return $maxDepth;
    }

    /**
     * Check if column values are present.
     *
     * @param array $columnValues
     *
     * @return bool
     */
    private function hasColumnValues(array $columnValues): bool
    {
        return isset($columnValues) && !empty($columnValues);
    }

    /**
     * Check if column titles are present.
     *
     * @param array $this->data
     *
     * @return bool
     */
    private function hasColumnTitles(): bool
    {
        return !empty($this->data['accordionHeadings']) && is_array($this->data['accordionHeadings']);
    }
}
