<?php

namespace Modularity\Module\Posts;

use Modularity\Helper\WpQueryFactory\WpQueryFactory;
use Modularity\Helper\WpService;
use Modularity\Module\Posts\Helper\DomainChecker;
use Modularity\Module\Posts\Helper\GetArchiveUrl;
use Modularity\Module\Posts\Helper\GetPosts\GetPostsInterface;
use Modularity\Module\Posts\Helper\GetPosts\PostsResultInterface;
use Modularity\Module\Posts\Helper\GetPosts\PostTypesFromSchemaType\PostTypesFromSchemaTypeResolver;
use Modularity\Module\Posts\Private\PrivateController;
use Modularity\Module\Posts\Helper\GetPosts\{
    GetPosts,
    GetPostsFromMultipleSites
};
use Modularity\Module\Posts\Helper\GetPosts\UserGroupResolver\UserGroupResolver;

/**
 * Class Posts
 * @package Modularity\Module\Posts
 */
class Posts extends \Modularity\Module
{
    public $slug = 'posts';
    public $supports = [];
    public array $fields = [];
    public $blockSupports = array(
        'align' => ['full']
    );
    public GetPostsInterface $getPostsHelper;
    public $archiveUrlHelper;
    public string $postStatus;
    public DomainChecker $domainChecker;
    private PrivateController $privateController;

    private $sliderCompatibleLayouts = ['items', 'news', 'index', 'grid', 'features-grid', 'segment'];

    public function init()
    {
        $this->nameSingular     = __('Posts', 'modularity');
        $this->namePlural       = __('Posts', 'modularity');
        $this->description      = __('Outputs selected posts in specified layout', 'modularity');

        // Private controller
        $this->privateController = new PrivateController($this);
        
        // Saves meta data to expandable list posts
        new \Modularity\Module\Posts\Helper\AddMetaToExpandableList();

        // Populate schema types field
        add_filter('acf/load_field/name=posts_data_schema_type', [$this, 'loadSchemaTypesField']);
        
        // Populate schema types field
        add_filter('acf/load_field/name=posts_data_network_sources', [$this, 'loadNetworkSourcesField']);

        //Add full width data to view
        add_filter('Modularity/Block/Data', array($this, 'blockData'), 50, 3);
        add_filter(
            'acf/fields/post_object/query/name=posts_data_posts', 
            array($this, 'removeUnwantedPostTypesFromManuallyPicked'), 10, 3
        );

        add_filter(
            'acf/load_field/name=taxonomy_display',
            array($this, 'loadTaxonomyDisplayField')
        );
        
        // Helpers
        $this->archiveUrlHelper = new GetArchiveUrl();
        new PostsAjax($this);
    }

    /**
     * Load schema types field
     *
     * @param array $field
     * @return array
     */
    public function loadSchemaTypesField(array $field = []):array {
        if(get_post_type() === 'acf-field-group') {
            return $field;
        }

        global $wpdb;
        $schemaTypesService = new \Municipio\SchemaData\Utils\SchemaTypesInUse($wpdb);

        // Set options. E.g. ["Event" => "Event", "Article" => "Article"]...
        $field['choices'] = array_combine( $schemaTypes = $schemaTypesService->getSchemaTypesInUse(), $schemaTypes );
        return $field;
    }

    /**
     * Load taxonomy display field
     *
     * @param array $field
     * @return array
     */
    public function loadTaxonomyDisplayField(array $field = []): array 
    {
        $taxonomies = get_taxonomies([
            'public' => true
        ], 'objects');

        $choices = [];
        foreach ($taxonomies as $taxonomyName => $taxonomyObj) {
            $choices[$taxonomyName] = $taxonomyObj->labels->singular_name;
        }

        $field['choices'] = $choices;

        return $field;
    }

    public function loadNetworkSourcesField(array $field = []) :array 
    {
        
        if(!is_multisite() || get_post_type() === 'acf-field-group') {
            return $field;
        }

        $field['choices'] = [];

        foreach (get_sites(['number' => 0]) as $site) {
            switch_to_blog($site->blog_id);
            $field['choices'][$site->blog_id] = get_bloginfo('name');
            restore_current_blog();
        }

        return $field;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        $data = [];
        $this->fields = $this->getFields();

        $this->domainChecker = new DomainChecker($this->fields);
        $data['posts_display_as'] = $this->fields['posts_display_as'] ?? false;
        $data['display_reading_time'] = !empty($this->fields['posts_fields']) && in_array('reading_time', $this->fields['posts_fields']) ?? false;

        // Posts
        $data['preamble']             = $this->fields['preamble'] ?? false;
        $data['posts_fields']         = $this->fields['posts_fields'] ?? [];
        $data['posts_data_post_type'] = $this->fields['posts_data_post_type'] ?? false;
        $data['posts_data_source']    = $this->fields['posts_data_source'] ?? false;
        $data['postsSources']         = $this->fields['posts_data_network_sources'] ?? [];

        $postsAndPaginationData = $this->getPostsResult();
        $data['posts']          = $postsAndPaginationData->getPosts();
        $data['stickyPosts']    = $postsAndPaginationData->getStickyPosts();

        if( !empty($this->fields['posts_pagination']) && $this->fields['posts_pagination'] === 'page_numbers' ) {
            $data['maxNumPages'] = $postsAndPaginationData->getNumberOfPages();
            $data['paginationArguments'] = $this->getPaginationArguments($data['maxNumPages'], $this->getPageNumber());
        } else {
            $data['paginationArguments'] = null;
        }

        // Sorting
        $data['sortBy'] = false;
        $data['orderBy'] = false;
        if (isset($this->fields['posts_sort_by']) && substr($this->fields['posts_sort_by'], 0, 9) === '_metakey_') {
            $data['sortBy'] = 'meta_key';
            $data['sortByKey'] = str_replace('_metakey_', '', $this->fields['posts_sort_by']);
        }

        $data['order'] = isset($this->fields['posts_sort_order']) ? $this->fields['posts_sort_order'] : 'asc';

        // Setup filters
        $filters = [
            'orderby' => sanitize_text_field($data['sortBy']),
            'order' => sanitize_text_field($data['order'])
        ];

        if ($data['sortBy'] == 'meta_key') {
            $filters['meta_key'] = $data['sortByKey'];
        }

        $data['filters'] = [];

        if (isset($this->fields['posts_taxonomy_filter']) && $this->fields['posts_taxonomy_filter'] === true && !empty($this->fields['posts_taxonomy_type'])) {
            $taxType = $this->fields['posts_taxonomy_type'];
            $taxValues = (array)$this->fields['posts_taxonomy_value'];
            $taxValues = implode('|', $taxValues);

            $data['filters']["{$taxType}[]"] = $taxValues;
        }

        //Get archive link
        $data['archiveLinkUrl'] = $this->archiveUrlHelper->getArchiveUrl(
            $data['posts_data_post_type'],
            $this->fields ?? null
        );

        // Archive link title
        $data['archiveLinkTitle'] = $this->fields['archive_link_title'] ?? null;

        // Archive link position
        $data['archiveLinkAbovePosts'] = $this->fields['archive_link_above_posts'] ?? false;

        //Add filters to archive link
        if($data['archiveLinkUrl'] && is_array($data['filters']) && !empty($data['filters'])) {
            $data['archiveLinkUrl'] .= "?" . http_build_query($data['filters']);
        }

        $data['ariaLabels'] =  (object) [
            'prev' => __('Previous slide', 'modularity'),
            'next' => __('Next slide', 'modularity'),
        ];

        if ($this->getID()) {
            $data['sliderId'] = $this->getID();
        } else {
            $data['sliderId'] = uniqid();
            $data['ID'] = uniqid();
        }

        $data['classList'] = [];

        $data['lang'] = [
            'showMore' => __('Show more', 'modularity'),
            'readMore' => __('Read more', 'modularity'),
            'save'        => __('Save', 'modularity'),
            'cancel'      => __('Cancel', 'modularity'),
            'description' => __('Description', 'modularity'),
            'name'        => __('Name', 'modularity'),
            'saving'      => __('Saving', 'modularity'),
            'error'       => __('An error occurred and the data could not be saved. Please try again later', 'modularity'),
            'changeContent' => __('Change the lists content', 'modularity'),
        ];

        return $data;
    }

    /**
     * Get pagination query var name.
     * 
     * @return string
     */
    private function getPaginationQueryVarName():string {
        return "mod-{$this->slug}-{$this->getID()}-page";
    }

    /**
     * Get current page number
     * 
     * @return int Default is 1
     */
    private function getPageNumber():int {
        return filter_input(INPUT_GET, $this->getPaginationQueryVarName(), FILTER_VALIDATE_INT) ?: 1;
    }

    /**
     * Get pagination arguments for page numbers.
     * 
     * @param int $maxNumPages
     * @param int $currentPage
     * @return array
     */
    private function getPaginationArguments(int $maxNumPages, int $currentPage):array {

        if ($maxNumPages < 2) {
            return [];
        }
        
        $listItemOne = [
            'href' => remove_query_arg($this->getPaginationQueryVarName()),
            'label' => __("First page", 'modularity')
        ];

        $listItems = array_map(function($pageNumber) {
            return [
                'href' => add_query_arg($this->getPaginationQueryVarName(), $pageNumber),
                'label' => sprintf(__("Page %d", 'modularity'), $pageNumber)
            ];
        }, range(2, $maxNumPages));

        return [
            'list' => array_merge([$listItemOne], $listItems),
            'current' => $currentPage,
            'linkPrefix' => $this->getPaginationQueryVarName()
        ];
    }

    /**
     * Add full width setting to frontend.
     *
     * @param [array] $viewData
     * @param [array] $block
     * @param [object] $module
     * @return array
     */
    public function blockData($viewData, $block, $module)
    {
        $viewData['noGutter'] = false;
        if (in_array($block['name'], ['posts', 'acf/posts']) && $block['align'] == 'full') {
            if (!is_admin()) {
                $viewData['stretch'] = true;
            }
            $viewData['noGutter'] = true;
        }

        return $viewData;
    }

    /**
     * Removes unwanted post types from the manually picked post types.
     *
     * @param array $args The arguments for the query.
     * @param string $field The field name.
     * @param int $id The ID of the module.
     * @return array The modified arguments.
     */
    public function removeUnwantedPostTypesFromManuallyPicked($args, $field, $id) 
    {
        $skipablePostTypes = ['attachment'];

        $args['post_type'] = array_filter($args['post_type'] ?? [], function($postType) use ($skipablePostTypes) {
            return !in_array($postType, $skipablePostTypes);
        });

        return $args;
    }

    /**
     * @return false|string
     */
    public function template()
    { 
        $template = !empty($this->data['posts_display_as']) ? $this->data['posts_display_as'] : 'list';

        if (!empty($this->fields['show_as_slider']) && in_array($this->fields['posts_display_as'], $this->sliderCompatibleLayouts, true)) {
            $template = 'slider';
        }
        

        $template = $this->replaceDeprecatedTemplate($template);
        $this->getTemplateData($template);

        $this->data['template'] = $template;

        return apply_filters(
            'Modularity/Module/Posts/template',
            $template . '.blade.php',
            $this,
            $this->data,
            $this->fields
        );
    }

    /**
     * @param $template
     */
    public function getTemplateData(string $template = '', array $data = array())
    {
        if (empty($template)) {
            return false;
        }

        if (!empty($data)) {
            $this->data = $data;
        }

        $template = explode('-', $template);
        $template = array_map('ucwords', $template);
        $template = implode('', $template);

        $class = '\Modularity\Module\Posts\TemplateController\\' . $template . 'Template';

        $this->data['meta']['posts_display_as'] = $this->replaceDeprecatedTemplate($this->data['posts_display_as']);

        if (class_exists($class)) {
            $controller = new $class($this);
            $this->data = array_merge($this->data, $controller->data);
            $this->data = $this->privateController->decorateData($this->data, $this->fields);
        }
    }

    /**
     * Converts an associative array to an object.
     *
     * This function takes an associative array and converts it into an object by first
     * encoding the array as a JSON string and then decoding it back into an object.
     * The resulting object will have properties corresponding to the keys in the original array.
     *
     * @param array $array The associative array to convert to an object.
     *
     * @return object Returns an object representing the associative array.
     */
    public function arrayToObject($array)
    {
        if(!is_array($array)) {
            return $array;
        }

        return json_decode(json_encode($array)); 
    }

    /**
     * Get posts and pagination data.
     *
     * @return PostsResultInterface
     */
    public function getPostsResult(): PostsResultInterface
    {
        $stickyPostHelper = new \Municipio\StickyPost\Helper\GetStickyOption( new \Municipio\StickyPost\Config\StickyPostConfig(), WpService::get() );
        $postTypesFromSchemaTypeResolver = new PostTypesFromSchemaTypeResolver();

        if(!empty($this->fields['posts_data_network_sources'])){
            global $wpdb;
            $this->getPostsHelper = new GetPostsFromMultipleSites(
                $this->fields,
                $this->getPageNumber(),
                array_map(fn($siteOption) => $siteOption['value'], $this->fields['posts_data_network_sources']),
                $wpdb,
                WpService::get(),
                $postTypesFromSchemaTypeResolver,
                new UserGroupResolver(WpService::get())
            );
        } else {
            $this->getPostsHelper = new GetPosts(
                $this->fields, 
                $this->getPageNumber(), 
                $stickyPostHelper, 
                WpService::get(), 
                new WpQueryFactory(), 
                $postTypesFromSchemaTypeResolver
            );
        }

        return $this->getPostsHelper->getPosts();
    }

    /**
     * For version 3.0 - Replace old post templates with existing replacement.
     * @param $templateSlug
     * @return mixed
     */
    public function replaceDeprecatedTemplate($templateSlug)
    {
        // Add deprecated template/replacement slug to array.
        $deprecatedTemplates = [
            'items' => 'index',
        ];

        if (array_key_exists($templateSlug, $deprecatedTemplates)) {
            return  $deprecatedTemplates[$templateSlug];
        }

        return $templateSlug;
    }

    public function adminEnqueue() {

        $wpService = WpService::get();
        $getCurrentPostId = fn() => $wpService->isArchive() ? false : $wpService->getTheID();

        wp_register_script('mod-posts-taxonomy-filtering', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('js/mod-posts-taxonomy-filtering.js'));
        wp_localize_script('mod-posts-taxonomy-filtering', 'modPostsTaxonomyFiltering', [
            'currentPostID' => $getCurrentPostId(),
        ]);
        wp_enqueue_script('mod-posts-taxonomy-filtering');
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
