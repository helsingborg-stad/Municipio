<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;

class HooksAdder
{
    private ResourceRegistryInterface $resourceRegistry;
    private ModifiersHelperInterface $modifiersHelper;

    public function __construct(ResourceRegistryInterface $resourceRegistry, ModifiersHelperInterface $modifiersHelper)
    {
        $this->resourceRegistry = $resourceRegistry;
        $this->modifiersHelper = $modifiersHelper;
    }

    public function addHooks()
    {
        add_action('clean_post_cache', [new ModifyCleanPostCache(), 'handle'], 10, 2);
        add_action('clean_object_term_cache', [new ModifyCleanObjectTermCache($this->resourceRegistry), 'handle'], 10, 2);
        add_filter('post_type_link', [new ModifyPostTypeLink($this->resourceRegistry), 'handle'], 10, 2);
        add_filter('posts_results', [new ModifyPostsResults($this->modifiersHelper), 'handle'], 10, 2);
        add_filter('default_post_metadata', [new ModifyDefaultPostMetaData($this->modifiersHelper), 'handle'], 100, 5);
        add_filter('acf/pre_load_value', [new ModifyPreLoadAcfValue($this->modifiersHelper), 'handle'], 10, 3);
        add_filter('wp_get_attachment_image_src', [new ModifyWpGetAttachmentImageSrc($this->modifiersHelper), 'handle'], 10, 4);
        add_filter('terms_pre_query', [new ModifyTermsPreQuery($this->resourceRegistry, $this->modifiersHelper), 'handle'], 10, 2);
        add_filter('get_object_terms', [new ModifyGetObjectTerms($this->resourceRegistry, $this->modifiersHelper), 'handle'], 10, 4);
        add_filter('Municipio/Archive/getTaxonomyFilters/option/value', [new ModifyMunicipioArchiveGetTaxonomyFiltersOptionValue($this->resourceRegistry), 'handle'], 10, 3);
        add_filter('Municipio/Breadcrumbs/Items', [new ModifyMunicipioBreadcrumbsItems($this->resourceRegistry), 'handle'], 10, 3);
        add_filter('Municipio/Content/ResourceFromApi/ConvertRestApiPostToWPPost', [new ModifyMunicipioContentResourceFromApiConvertRestApiPostToWPPost($this->resourceRegistry), 'handle'], 10, 3);
    }
}
