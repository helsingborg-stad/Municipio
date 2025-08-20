<?php

namespace Municipio;

use AcfService\AcfService;
use HelsingborgStad\BladeService\BladeService;
use Municipio\AcfFieldContentModifiers\AcfFieldContentModifierRegistrarInterface;
use Municipio\Api\RestApiEndpointsRegistry;
use Municipio\BrandedEmails\ApplyMailHtmlTemplate;
use Municipio\BrandedEmails\HtmlTemplate\Config\HtmlTemplateConfigService;
use Municipio\BrandedEmails\HtmlTemplate\DefaultHtmlTemplate;
use Municipio\Comment\OptionalDisableDiscussionFeature;
use Municipio\Comment\OptionalHideDiscussionWhenLoggedOut;
use Municipio\SchemaData\Config\SchemaDataConfigInterface;
use Municipio\Content\ResourceFromApi\Api\ResourceFromApiRestController;
use Municipio\Content\ResourceFromApi\Modifiers\HooksAdder;
use Municipio\Content\ResourceFromApi\Modifiers\ModifiersHelper;
use Municipio\Content\ResourceFromApi\PostTypeFromResource;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Content\ResourceFromApi\TaxonomyFromResource;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\MenuBuilder;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Helper\ResourceFromApiHelper;
use Municipio\HooksRegistrar\HooksRegistrarInterface;
use Municipio\Helper\Listing;
use WP_Post;
use wpdb;
use WpService\WpService;
use Municipio\Helper\User\Config\UserConfig;
use Municipio\Helper\User\User;
use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\SchemaData\SchemaDataFeature;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostFactory;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Municipio\SchemaData\Utils\SchemaTypesInUse;

/**
 * Class App
 * @package Municipio
 */
class App
{
    /**
     * App constructor.
     */
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
        private HooksRegistrarInterface $hooksRegistrar,
        private AcfFieldContentModifierRegistrarInterface $acfFieldContentModifierRegistrar,
        private SchemaDataConfigInterface $schemaDataConfig,
        private wpdb $wpdb
    ) {
        /**
         * Run generic custom actions
         */
        (new \Municipio\Actions\Admin\PostPageEditAction($this->wpService))->addHooks();

        /**
         * Upgrade
         */
        new \Municipio\Upgrade($this->wpService, $this->acfService);


        /**
         * Upgrade
         */
        $menuDirector = new MenuDirector();
        $menuBuilder  = new MenuBuilder(
            new MenuConfig(),
            $this->acfService,
            $this->wpService
        );

        /*
         * Helpers
         */
        $userGroupConfig  = new \Municipio\UserGroup\Config\UserGroupConfig($this->wpService);
        $userHelperConfig = new \Municipio\Helper\User\Config\UserConfig();
        $userHelper       = new \Municipio\Helper\User\User(
            $this->wpService,
            $this->acfService,
            $userHelperConfig,
            $userGroupConfig,
            new \Municipio\Helper\Term\Term($this->wpService, $this->acfService),
            new \Municipio\Helper\SiteSwitcher\SiteSwitcher($this->wpService, $this->acfService)
        );

        /**
         * User group
         */
        $userGroupRestrictionConfig = new \Municipio\Admin\Private\Config\UserGroupRestrictionConfig();

        $mainQueryUserGroupRestriction = new \Municipio\Admin\Private\MainQueryUserGroupRestriction(
            $this->wpService,
            $userHelper,
            $userGroupRestrictionConfig
        );

        /**
         * Allow posts in private visibility to have further conditions to be shown.
         */
        (new \Municipio\Admin\Private\PrivateAcfFields($this->wpService))->addHooks();

        /**
         * Template
         */
        new \Municipio\Template(
            $menuBuilder,
            $menuDirector,
            $this->acfService,
            $this->wpService,
            $mainQueryUserGroupRestriction,
            new \Municipio\Helper\SiteSwitcher\SiteSwitcher($this->wpService, $this->acfService),
            new CreatePostObjectFromWpPost(
                $this->wpService,
                $this->acfService,
                (new SchemaObjectFromPostFactory(
                    $this->schemaDataConfig,
                    $this->wpService,
                    new \Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes(),
                    new SchemaPropertyValueSanitizer()
                ))->create()
            ),
            $userHelper
        );

        /**
         * Theme
         */
        new \Municipio\Theme\Enqueue();
        new \Municipio\Theme\Support();
        new \Municipio\Theme\Sidebars();
        new \Municipio\Theme\General();
        new \Municipio\Theme\CustomCodeInput();
        new \Municipio\Theme\Blog();
        new \Municipio\Theme\FileUploads();
        new \Municipio\Theme\Archive();
        new \Municipio\Theme\CustomTemplates();
        new \Municipio\Theme\Navigation(new SchemaTypesInUse($this->wpdb));
        new \Municipio\Theme\Icon();
        new \Municipio\Theme\Forms();

        new \Municipio\Search\General();
        new \Municipio\Search\Algolia();

        /**
         * Content
         */
        new \Municipio\Content\CustomPostType();
        new \Municipio\Content\CustomTaxonomy();
        new \Municipio\Content\PostFilters();
        (new \Municipio\Content\PostFilters\RemoveExpiredEventsFromMainArchiveQuery($this->wpService, $this->schemaDataConfig))->addHooks();
        new \Municipio\Content\ShortCode();
        new \Municipio\Content\Images();
        new \Municipio\Content\Cache();
        new \Municipio\Content\IframePosterImage();

        /**
         * Post decorators
         */
        $this->wpService->addFilter('Municipio/Helper/Post/postObject', function (WP_Post $post) {

            // Place
            $decorator = new \Municipio\PostDecorators\ApplyBookingLinkToPlace($this->acfService);
            $decorator = new \Municipio\PostDecorators\ApplyInfoListToPlace(
                $this->acfService,
                new Listing(),
                $decorator
            );

            return $decorator->apply($post);
        }, 10, 1);

        /**
         * Resources from API
         */

        // Register the actual post type to be used for resources.
        $resourcePostType = new \Municipio\Content\ResourceFromApi\ResourcePostType($this->wpService);
        $resourcePostType->addHooks();

        // Set up registry.

        $resourceRegistry = new \Municipio\Content\ResourceFromApi\ResourceRegistry\ResourceRegistry();

        add_action('init', function () use ($resourceRegistry) {

            $resourceRegistry->registerResources();

            $postTypeResources       = $resourceRegistry->getByType(ResourceType::POST_TYPE);
            $sortedPostTypeResources = $resourceRegistry->sortByParentPostType($postTypeResources);

            foreach ($sortedPostTypeResources as $resource) {
                $registrar = new PostTypeFromResource($resource);
                $registrar->register();
            }

            foreach ($resourceRegistry->getByType(ResourceType::TAXONOMY) as $resource) {
                $registrar = new TaxonomyFromResource($resource);
                $registrar->register();
            }
        });

        // Make resources available to the helper class.
        ResourceFromApiHelper::initialize($resourceRegistry);

        // Add hooks for modifiers. Modifiers are used to modify the output of resources through filters and actions.
        $modifiersHelper = new ModifiersHelper($resourceRegistry);
        $hooksAdder      = new HooksAdder($resourceRegistry, $modifiersHelper);
        $hooksAdder->addHooks();

        // Add REST API endpoints for resources.
        add_action('rest_api_init', function () use ($resourceRegistry) {
            $resources = $resourceRegistry->getByType(ResourceType::POST_TYPE);

            if (empty($resources)) {
                return;
            }

            foreach ($resourceRegistry->getByType(ResourceType::POST_TYPE) as $resource) {
                $controller = new ResourceFromApiRestController($resource->getName());
                $controller->register_routes();
            }
        });

        /**
         * Oembed
         */
        new \Municipio\Oembed\OembedFilters();

        /**
         * Language
         */

        new \Municipio\Language();

        /**
         * Widget
         */
        new \Municipio\Widget\Widgets();

        /**
         * Comments
         */
        new \Municipio\Comment\HoneyPot();
        new \Municipio\Comment\Likes();
        new \Municipio\Comment\Filters();
        new \Municipio\Comment\Form();
        $this->hooksRegistrar->register(new OptionalDisableDiscussionFeature($this->wpService, $this->acfService));
        $this->hooksRegistrar->register(new OptionalHideDiscussionWhenLoggedOut($this->wpService, $this->acfService));

        /**
         * Admin
         */
        new \Municipio\Admin\Gutenberg\Gutenberg();
        new \Municipio\Admin\General();

        new \Municipio\Admin\Gutenberg\Blocks\BlockManager();

        new \Municipio\Admin\Options\Theme();
        new \Municipio\Admin\Options\Timestamp();
        new \Municipio\Admin\Options\GoogleTranslate();
        new \Municipio\Admin\Options\ContentEditor();
        new \Municipio\Admin\Options\AttachmentConsent();

        new \Municipio\Admin\Acf\PrefillIconChoice($this->wpService);
        new \Municipio\Admin\Acf\PrefillColor($this->wpService);
        new \Municipio\Admin\Acf\ImageAltTextValidation();

        new \Municipio\Admin\Roles\General($this->wpService);
        new \Municipio\Admin\Roles\Editor($userHelper);

        new \Municipio\Admin\UI\BackEnd();
        new \Municipio\Admin\UI\FrontEnd();
        new \Municipio\Admin\UI\Editor();

        new \Municipio\Admin\TinyMce\LoadPlugins();

        /* Integration: MiniOrange */
        $moveAdminPageToSettings = new \Municipio\Integrations\MiniOrange\MoveAdminPageToSettings($this->wpService);
        $this->hooksRegistrar->register($moveAdminPageToSettings);

        /* Admin uploads */
        $uploads = new \Municipio\Admin\Uploads();
        $uploads->addHooks();

        /**
         * Api
         */
        RestApiEndpointsRegistry::add(new \Municipio\Api\Media\Sideload());
        RestApiEndpointsRegistry::add(new \Municipio\Api\Navigation\Children($menuBuilder, $menuDirector));
        RestApiEndpointsRegistry::add(new \Municipio\Api\Navigation\ChildrenRender($menuBuilder, $menuDirector));
        RestApiEndpointsRegistry::add(new \Municipio\Api\View\Render());
        RestApiEndpointsRegistry::add(new \Municipio\Api\PlaceSearch\PlaceSearchEndpoint($this->wpService));

        $pdfHelper    = new \Municipio\Api\Pdf\PdfHelper();
        $pdfGenerator = new \Municipio\Api\Pdf\PdfGenerator($pdfHelper);
        $pdfGenerator->addHooks();

        /**
         * Customizer
         */
        new \Municipio\Customizer($this->wpService, $this->wpdb);

        /**
         * Block customizations
         */
        new \Municipio\Blocks\Columns();

        add_filter('Modularity/CoreTemplatesSearchPaths', function ($paths) {
            $paths[] = get_stylesheet_directory() . '/views/v3';
            $paths[] = get_template_directory() . '/views/v3';
            return $paths;
        });

        /**
         * Imported post type design
         */
        $this->setupPostTypeDesign();

        /**
         * Branded emails
         */
        $this->trySetupBrandedEmails();

        /**
         * Apply schema.org data to posts
         */
        (new SchemaDataFeature(
            $this->wpService,
            $this->acfService,
            $this->hooksRegistrar,
            $this->acfFieldContentModifierRegistrar,
            $this->schemaDataConfig,
            $this->wpdb
        ))->enable();

        /**
         * Image convert
         */
        $this->setupImageConvert();

        /**
         * Component Context filters
         */
        $this->setupComponentContextFilters();

        /**
         * Sticky posts
         */
        $this->setupStickyPosts();

        /**
         * Login screen
         */
        $this->setupLoginLogout();

        /**
         * UserGroup feature
         */
        $this->setupUserGroupFeature();

        /**
         * MiniOrange integration
         */
        $this->setUpMiniOrangeIntegration();

        /**
         * Broken links
         */
        $this->setUpBrokenLinksIntegration();

        /**
         * Setup common options
         */
        $this->setUpCommonFieldGroups();

        /**
         * Setup global notices
         */
        $this->setUpGlobalNotices();

        /**
         * Setup Mirrored Post
         */
        (new \Municipio\MirroredPost\MirroredPostFeature($this->wpService))->enable();

        /**
         * Setup Table of Contents
         */
        (new \Municipio\Toc\TocFeature(
            $this->wpService,
            $this->acfService
        )
        )->enable();

        /**
         * Setup Accessibility Statement
         */
        $this->setupAccessibilityStatement();
    }

    /**
     * Setup accessibility statement.
     * @return void
     */
    public function setupAccessibilityStatement(): void
    {
        $accessibilityStatement = new \Municipio\A11yStatement\A11yStatement($this->wpService, $this->acfService);
        $accessibilityStatement->addHooks();
    }

    /**
     * Set up the global notices feature.
     *
     * This method initializes the global notices feature by creating an instance of the
     * RegisterGlobalNoticesFieldGroupsAdminPage class and passing the WordPress service instance.
     * The method then adds the hooks to the WordPress service instance.
     * @return void
     */
    private function setUpGlobalNotices(): void
    {
        //Admin
        $registerGlobalNoticesFieldGroupsAdminPage = new \Municipio\GlobalNotices\RegisterGlobalNoticesFieldGroupsAdminPage($this->wpService, $this->acfService);
        $registerGlobalNoticesFieldGroupsAdminPage->addHooks();

        //Get and apply global notices
        $getAndApplyGlobalNotices = new \Municipio\GlobalNotices\GetAndApplyGlobalNotices($this->wpService, $this->acfService, new \Municipio\GlobalNotices\GlobalNoticesConfig());
        $getAndApplyGlobalNotices->addHooks();
    }

    /**
     * Set up the common options feature.
     *
     * This method initializes the common options feature by creating an instance of the
     * RegisterCommonOptionsAdminPage class and passing the WordPress service instance.
     *
     * @return void
     */
    private function setUpCommonFieldGroups(): void
    {
        //Init dependencies
        $siteSwitcher = new \Municipio\Helper\SiteSwitcher\SiteSwitcher($this->wpService, $this->acfService);
        $config       = new \Municipio\CommonFieldGroups\CommonFieldGroupsConfig(
            $this->wpService,
            $this->acfService,
            $siteSwitcher
        );

        //Check if feature is enabled
        if ($config->isEnabled() === false) {
            return;
        }

        //Admin page
        $registerCommonFieldGroupsOptionsAdminPage = new \Municipio\CommonFieldGroups\RegisterCommonFieldGroupsOptionsAdminPage($this->wpService, $this->acfService);
        $registerCommonFieldGroupsOptionsAdminPage->addHooks();

        //Populate admin page fields
        $populateCommonFieldGroupSelect = new \Municipio\CommonFieldGroups\PopulateCommonFieldGroupSelect($this->wpService, $this->acfService, $config);
        $populateCommonFieldGroupSelect->addHooks();

        //Disable fields
        $disableFieldsThatAreCommonlyManagedOnSubsites = new \Municipio\CommonFieldGroups\DisableFieldsThatAreCommonlyManagedOnSubsites($this->wpService, $this->acfService, $siteSwitcher, $config);
        $disableFieldsThatAreCommonlyManagedOnSubsites->addHooks();

        //SubField Resolvers
        $subFieldValueResolver = new \Municipio\CommonFieldGroups\SubFieldValueResolver\NullResolver();
        $subFieldValueResolver = new \Municipio\CommonFieldGroups\SubFieldValueResolver\ResolveFromGetOption($this->wpService, $subFieldValueResolver);
        $subFieldValueResolver = new \Municipio\CommonFieldGroups\SubFieldValueResolver\ResolveValueFromSelectFieldThatReturnsBothLabelAndValue($this->wpService, $subFieldValueResolver);

        //Modify field choices
        $filterGetFieldToRetriveCommonValues = new \Municipio\CommonFieldGroups\FilterGetFieldToRetriveCommonValues($this->wpService, $this->acfService, $siteSwitcher, $config, $subFieldValueResolver);
        $filterGetFieldToRetriveCommonValues->addHooks();
    }

    /**
     * Sets up the broken links integration.
     *
     * This method initializes the broken links integration by creating an instance of the
     * RedirectToLoginWhenInternalContext class and passing the WordPress service instance.
     *
     * @return void
     */

    private function setUpBrokenLinksIntegration(): void
    {
        $config = new \Municipio\Integrations\BrokenLinks\Config\BrokenLinksConfig();
        if ($config->isEnabled() === false) {
            return;
        }

        $redirect = new \Municipio\Integrations\BrokenLinks\RedirectToLoginWhenInternalContext($this->wpService, $config);
        $redirect->addHooks();
    }

    /**
     * Sets up the sticky posts feature.
     *
     * This method initializes the sticky posts feature by creating an instance of the
     * StickyPosts class and passing
     */
    private function setupStickyPosts(): void
    {
        $stickyPostConfig = new \Municipio\StickyPost\Config\StickyPostConfig();
        $stickyPostHelper = new \Municipio\StickyPost\Helper\GetStickyOption(
            $stickyPostConfig,
            $this->wpService
        );
        (new \Municipio\StickyPost\AddStickyCheckboxForPost(
            $stickyPostHelper,
            $this->wpService
        ))->addHooks();

        (new \Municipio\StickyPost\AddStickyLabelToPost(
            $stickyPostHelper,
            $this->wpService
        ))->addHooks();
    }

    /**
     * Set up the custom login screen.
     *
     * This method is responsible to apply design changes to the login screen.
     *
     * @return void
     */
    private function setupLoginLogout(): void
    {
        //Needs setUser to be called before using the user object
        $userHelper = new User(
            $this->wpService,
            $this->acfService,
            new UserConfig(),
            new \Municipio\UserGroup\Config\UserGroupConfig($this->wpService),
            new \Municipio\Helper\Term\Term($this->wpService, $this->acfService),
            new \Municipio\Helper\SiteSwitcher\SiteSwitcher($this->wpService, $this->acfService)
        );

        $filterAuthUrls = new \Municipio\Admin\Login\RelationalLoginLogourUrls($this->wpService);
        $filterAuthUrls->addHooks();

        $addLoginAndLogoutNotices = new \Municipio\Admin\Login\AddLoginAndLogoutNotices($this->wpService, $this->acfService, $userHelper, new UserConfig());
        $addLoginAndLogoutNotices->addHooks();

        $logUserLoginTime = new \Municipio\Admin\Login\LogUserLoginTime($this->wpService);
        $logUserLoginTime->addHooks();

        $registerLoginLogoutOptionsPage = new \Municipio\Admin\Login\RegisterLoginLogoutOptionsPage($this->wpService, $this->acfService);
        $registerLoginLogoutOptionsPage->addHooks();

        $enqueueLoginScreenStyles = new \Municipio\Admin\Login\EnqueueLoginScreenStyles($this->wpService);
        $enqueueLoginScreenStyles->addHooks();

        $setLoginScreenLogotypeData = new \Municipio\Admin\Login\SetLoginScreenLogotypeData($this->wpService);
        $setLoginScreenLogotypeData->addHooks();

        $doNotHaltAuthWhenNonceIsMissing = new \Municipio\Admin\Login\DoNotHaltAuthWhenNonceIsMissing($this->wpService);
        $doNotHaltAuthWhenNonceIsMissing->addHooks();

        $redirectUserToGroupUrlIfIsPrefered = new \Municipio\Admin\Login\RedirectUserToGroupUrlIfIsPreferred($this->wpService, $userHelper);
        $redirectUserToGroupUrlIfIsPrefered->addHooks();
    }

    /**
     * Set up the user group feature.
     */
    private function setupUserGroupFeature(): void
    {
        $config = new \Municipio\UserGroup\Config\UserGroupConfig($this->wpService);

        if ($config->isEnabled() === false) {
            return;
        }

        // Setup dependencies
        $userGroupRestrictionConfig = new \Municipio\Admin\Private\Config\UserGroupRestrictionConfig();
        $userHelperConfig           = new \Municipio\Helper\User\Config\UserConfig();
        $siteSwitcher               = new \Municipio\Helper\SiteSwitcher\SiteSwitcher($this->wpService, $this->acfService);

        $userHelper = new \Municipio\Helper\User\User(
            $this->wpService,
            $this->acfService,
            $userHelperConfig,
            $config,
            new \Municipio\Helper\Term\Term($this->wpService, $this->acfService),
            $siteSwitcher
        );

        $getUserGroupTerms = new \Municipio\Helper\User\GetUserGroupTerms(
            $this->wpService,
            $config->getUserGroupTaxonomy(),
            new \Municipio\Helper\SiteSwitcher\SiteSwitcher($this->wpService, $this->acfService)
        );

        // Create user group taxonomy
        (new \Municipio\UserGroup\CreateUserGroupTaxonomy($this->wpService, $config, $siteSwitcher))->addHooks();

        // Add user group to users list
        (new \Municipio\UserGroup\DisplayUserGroupTaxonomyInUsersList($this->wpService, $config))->addHooks();

        // Add user group to admin menu
        (new \Municipio\UserGroup\DisplayUserGroupTaxonomyLinkInAdminUi($this->wpService, $config))->addHooks();

        // Add user group to user profile & populate
        (new \Municipio\UserGroup\DisplayUserGroupTaxonomyInUserProfile($this->wpService, $this->acfService, $config))->addHooks();

        // User group url
        (new \Municipio\UserGroup\PopulateUserGroupUrlBlogIdField($this->wpService))->addHooks();

        // Add user group select to edit post when private
        (new \Municipio\UserGroup\AddSelectUserGroupForPrivatePost(
            $this->wpService,
            $config->getUserGroupTaxonomy(),
            $userHelperConfig,
            $userGroupRestrictionConfig,
            $getUserGroupTerms
        ))->addHooks();

        // Restrict private posts to user group
        (new \Municipio\UserGroup\RestrictPrivatePostToUserGroup($this->wpService, $userHelper, $userGroupRestrictionConfig))->addHooks();

        // Redirect to user group url after SSO login if using MiniOrange plugin for SSO login
        (new \Municipio\UserGroup\RedirectToUserGroupUrlAfterSsoLogin($userHelper, $this->wpService))->addHooks();
    }

    /**
     * Set up the MiniOrange integration.
     *
     * This method is responsible for setting up the MiniOrange integration.
     *
     * @return void
     */
    private function setUpMiniOrangeIntegration(): void
    {
        $userHelper = new \Municipio\Helper\User\User(
            $this->wpService,
            $this->acfService,
            new \Municipio\Helper\User\Config\UserConfig(),
            new \Municipio\UserGroup\Config\UserGroupConfig($this->wpService),
            new \Municipio\Helper\Term\Term($this->wpService, $this->acfService),
            new \Municipio\Helper\SiteSwitcher\SiteSwitcher($this->wpService, $this->acfService)
        );

        $termHelper      = new \Municipio\Helper\Term\Term($this->wpService, $this->acfService);
        $userGroupConfig = new \Municipio\UserGroup\Config\UserGroupConfig($this->wpService);
        $config          = new \Municipio\Integrations\MiniOrange\Config\MiniOrangeConfig($this->wpService);

        if ($config->isEnabled() === false) {
            return;
        }

        //Require SSO login
        $requireSsoLogin = new \Municipio\Integrations\MiniOrange\RequireSsoLogin($this->wpService, $config);
        $requireSsoLogin->addHooks();

        //Map values to WordPress user
        $mappingProviders = [
            new \Municipio\Integrations\MiniOrange\Provider\DefaultProvider(),
        ];
        $attributeMapper  = new \Municipio\Integrations\MiniOrange\AttributeMapper($this->wpService, $config, ...$mappingProviders);
        $attributeMapper->addHooks();

        // Allow redirect after SSO login
        (new \Municipio\Integrations\MiniOrange\AllowRedirectAfterSsoLogin($this->wpService))->addHooks();

        if ($userGroupConfig->isEnabled() === false) {
            return;
        }

        // Set group as taxonomy
        $setGroupAsTaxonomy = new \Municipio\Integrations\MiniOrange\SetUserGroupFromSsoLoginGroup($this->wpService, $userHelper);
        $setGroupAsTaxonomy->addHooks();
    }

    /**
     * Sets up the component context filters.
     *
     * This method initializes the component context filters by creating instances of the
     * CurrentSidebar and CompressedCollections classes and passing the WordPress service instance.
     *
     * @return void
     */
    private function setupComponentContextFilters(): void
    {
        $currentSidebar = new \Municipio\Integrations\Component\ContextFilters\Sidebar\CurrentSidebar($this->wpService);
        $currentSidebar->addHooks();

        $compressedCollections = new \Municipio\Integrations\Component\ContextFilters\Sidebar\CompressedCollections($this->wpService, $currentSidebar);
        $compressedCollections->addHooks();
    }

    /**
     * Sets up the post type design.
     *
     * This method initializes functionality for handling
     * image conversion and resizing of images.
     *
     * @return void
     */
    private function setupImageConvert(): void
    {
        //Image convert config service
        $imageConvertConfig = new \Municipio\ImageConvert\Config\ImageConvertConfig(
            $this->wpService,
            'Municipio/ImageConvert',
            'webp',
            90,
            1920,
            5
        );

        //Check if image convert is enabled
        if ($imageConvertConfig->isEnabled() === false) {
            return;
        }

        //Init image convert filter
        $imageConvertFilter = new \Municipio\ImageConvert\ImageConvertFilter(
            $this->wpService,
            $imageConvertConfig
        );
        $imageConvertFilter->addHooks();

        //Makes shure that the image has full dataset in order to resize image
        $normalizeImageSize = new \Municipio\ImageConvert\NormalizeImageSize(
            $this->wpService,
            $imageConvertConfig
        );
        $normalizeImageSize->addHooks();

        //Calculate image dimensions, if there are any missing.
        $resolveMissingImageSize = new \Municipio\ImageConvert\Resolvers\MissingSize\ResolveMissingImageSize(
            $this->wpService,
            $imageConvertConfig
        );
        $resolveMissingImageSize->addHooks();

        //Create the missing intermidiate image
        $intermidiateImageHandler = new \Municipio\ImageConvert\IntermidiateImageHandler(
            $this->wpService,
            $imageConvertConfig
        );
        $intermidiateImageHandler->addHooks();

        //Resolve image to wp image contract (standard wordpress image array)
        $resolveToWpImageContract = new \Municipio\ImageConvert\ResolveToWpImageContract(
            $this->wpService,
            $imageConvertConfig
        );
        $resolveToWpImageContract->addHooks();
    }

    /**
     * Sets up the post type design.
     *
     * This method initializes the post type design by creating instances of the
     * SaveDesigns and SetDesigns classes and passing the option name and the
     * WordPress service instance.
     *
     * @return void
     */
    private function setupPostTypeDesign(): void
    {
        $optionName  = 'post_type_design';
        $apiUrl      = 'https://customizer.municipio.tech/id/';
        $saveDesigns =  new \Municipio\PostTypeDesign\SaveDesigns(
            $optionName,
            $this->wpService,
            new \Municipio\PostTypeDesign\ConfigFromPageId($this->wpService, $apiUrl)
        );

        $setDesigns = new \Municipio\PostTypeDesign\SetDesigns($optionName, $this->wpService);

        $this->hooksRegistrar->register($saveDesigns);
        $this->hooksRegistrar->register($setDesigns);
    }

    /**
     * Branded emails setup
     *
     * Enables branded html emails if enabled from theme options page.
     * Uses theme appearance to apply branding to all outgoing emails.
     */
    private function trySetupBrandedEmails(): void
    {
        $configService = new \Municipio\BrandedEmails\Config\BrandedEmailsConfigService($this->wpService);

        if ($configService->isEnabled() === false) {
            return;
        }

        $setMailContentType    = new \Municipio\BrandedEmails\SetMailContentType('text/html', $this->wpService);
        $convertMessageToHtml  = new \Municipio\BrandedEmails\ConvertMessageToHtml($this->wpService);
        $bladeService          = new BladeService([__DIR__ . '/BrandedEmails/HtmlTemplate/views']);
        $htmlTemplateConfig    = new HtmlTemplateConfigService($this->wpService);
        $emailHtmlTemplate     = new DefaultHtmlTemplate($htmlTemplateConfig, $this->wpService, $bladeService);
        $applyMailHtmlTemplate = new ApplyMailHtmlTemplate($emailHtmlTemplate, $this->wpService);

        $this->hooksRegistrar->register($setMailContentType);
        $this->hooksRegistrar->register($convertMessageToHtml);
        $this->hooksRegistrar->register($applyMailHtmlTemplate);
    }
}
