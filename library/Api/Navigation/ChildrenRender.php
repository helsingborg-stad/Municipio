<?php

namespace Municipio\Api\Navigation;

use Municipio\Api\RestApiEndpoint;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Helper\TranslatedLabels;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use WP_Error;

/**
 * Class ChildrenRender
 */
class ChildrenRender extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = '/navigation/children/render';

    /**
     * ChildrenRender constructor.
     *
     * @param MenuBuilderInterface $menuBuilder
     * @param MenuDirector         $menuDirector
     */
    public function __construct(private MenuBuilderInterface $menuBuilder, private MenuDirector $menuDirector)
    {
    }

    /**
     * @inheritDoc
     */
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods'             => 'GET',
            'callback'            => array($this, 'handleRequest'),
            'permission_callback' => '__return_true',
            'args'                => array(
                'pageId'     => array(
                    'required'          => true,
                    'validate_callback' => fn($input) => is_numeric($input),
                    'sanitize_callback' => fn($input) => (int)$input,
                ),
                'viewPath'   => array(
                    'required'          => false,
                    'sanitize_callback' => fn($input) => sanitize_text_field($input),
                ),
                'depth'      => array(
                    'required'          => false,
                    'validate_callback' => fn($input) => is_numeric($input),
                    'sanitize_callback' => fn($input) => (int)$input < 1 ? 1 : (int)$input,
                ),
                'identifier' => array(
                    'required'          => false,
                    'sanitize_callback' => fn($input) => sanitize_text_field($input),
                ),
            ),
        ));
    }

    /**
     * @inheritDoc
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $params = $request->get_params();

        $pageId     = (int) ($params['pageId'] ?? 0);
        $viewPath   = empty($params['viewPath']) ? 'partials.navigation.mobile' : $params['viewPath'];
        $depth      = !empty($params['depth']) ? $params['depth'] : 1;
        $lang       = TranslatedLabels::getLang();
        $identifier = $params['identifier'] ?? 'mobile';

        $menuItems = $this->resolveMenuItems($pageId, $identifier);

        $data = [
            'viewPath'  => $viewPath,
            'menuItems' => $menuItems,
            'homeUrl'   => esc_url(get_home_url()),
            'depth'     => $depth,
            'lang'      => $lang,
            'classList' => []
        ];

        $data = $this->getExtendedDropdownData($identifier, $data);


        try {
            $markup = render_blade_view($viewPath ?: 'partials.navigation.mobile', [
                    'menuItems' => $menuItems,
                    'homeUrl'   => esc_url(get_home_url()),
                    'depth'     => $depth,
                    'lang'      => $lang,
                    'classList' => []
            ]);
        } catch (\Exception $e) {
            return rest_ensure_response(new WP_Error(
                'render_error',
                __('An error occurred while rendering the menu.', 'municipio')
            ));
        }

        return rest_ensure_response(array(
            'parentId' => $pageId,
            'viewPath' => $viewPath,
            'markup'   => $markup
        ));
    }

    /**
     * Resolve menu items for the requested branch.
     *
     * @param int    $pageId The requested parent page ID.
     * @param string $identifier The menu identifier.
     *
     * @return array
     */
    private function resolveMenuItems(int $pageId, string $identifier): array
    {
        $directItems = $this->resolveDirectMenuItems($pageId, $identifier);
        if (!empty($directItems)) {
            return $directItems;
        }

        $this->menuDirector->setBuilder($this->menuBuilder);

        $menuName = NavigationBranchResolver::resolveMenuName($identifier);
        if ($menuName !== '') {
            $this->menuBuilder->setConfig(new MenuConfig($identifier, $menuName, false, false, true));
            $this->menuDirector->buildMixedPageTreeMenu();

            $menuItems = $this->menuBuilder->getMenu()->getMenu()['items'] ?? [];
            $branch    = NavigationBranchResolver::findChildren($menuItems, $pageId);

            if (is_array($branch)) {
                return $branch;
            }
        }

        $this->menuBuilder->setConfig(new MenuConfig($identifier, '', false, false, $pageId));
        $this->menuDirector->buildPageTreeMenu();

        return $this->menuBuilder->getMenu()->getMenu()['items'] ?? [];
    }

    /**
     * Resolve page tree items directly from the underlying post hierarchy.
     *
     * @param int    $pageId The requested parent page ID.
     * @param string $identifier The menu identifier.
     *
     * @return array
     */
    private function resolveDirectMenuItems(int $pageId, string $identifier): array
    {
        $pageForPostTypes = GetPageForPostTypeIds::getPageForPostTypeIds();
        $postType         = $pageForPostTypes[$pageId] ?? get_post_type($pageId);
        $parentId         = isset($pageForPostTypes[$pageId]) ? 0 : $pageId;

        if (!is_string($postType) || $postType === '') {
            return [];
        }

        $menuItems = GetPostsByParent::getPostsByParent($parentId, $postType);

        if (empty($menuItems) && $parentId === $pageId) {
            $translatedChildren = apply_filters('Municipio/Navigation/PageTree/Children', [], $pageId);
            $menuItems          = is_array($translatedChildren) ? $translatedChildren : [];
        }

        $menuItems = array_values(array_filter(array_map(
            fn (array $menuItem): ?array => $this->mapDirectMenuItem($menuItem, $identifier),
            $menuItems
        )));

        $menuItems = apply_filters('Municipio/Navigation/Items', $menuItems, $identifier);

        return is_array($menuItems) ? array_values($menuItems) : [];
    }

    /**
     * Map a raw post record into the menu item shape expected by the nav component.
     *
     * @param array  $menuItem The raw item.
     * @param string $identifier The menu identifier.
     *
     * @return ?array
     */
    private function mapDirectMenuItem(array $menuItem, string $identifier): ?array
    {
        $itemId = $menuItem['id'] ?? $menuItem['ID'] ?? null;

        if (!is_numeric($itemId) || (int) $itemId <= 0) {
            return null;
        }

        $itemId   = (int) $itemId;
        $postType = (string) ($menuItem['post_type'] ?? get_post_type($itemId));

        if ($postType === '') {
            return null;
        }

        $children           = GetPostsByParent::getPostsByParent($itemId, $postType);
        $translatedChildren = [];

        if (empty($children) && $postType === 'page') {
            $translatedChildren = apply_filters('Municipio/Navigation/PageTree/Children', [], $itemId);
            $translatedChildren = is_array($translatedChildren) ? $translatedChildren : [];
        }

        $hasChildren = !empty($children) || !empty($translatedChildren);

        $mappedItem = [
            'id'          => $itemId,
            'post_parent' => (int) ($menuItem['post_parent'] ?? 0),
            'post_type'   => $postType,
            'active'      => false,
            'ancestor'    => false,
            'label'       => (string) ($menuItem['label'] ?? $menuItem['post_title'] ?? get_the_title($itemId)),
            'href'        => (string) get_permalink($itemId),
            'children'    => $hasChildren,
        ];

        if ($hasChildren) {
            $fetchUrl = $this->buildFetchUrl($itemId, $identifier);
            $mappedItem['attributeList'] = [
                'data-fetch-url' => apply_filters(
                    'Municipio/Navigation/PageTree/FetchUrl',
                    $fetchUrl,
                    $mappedItem,
                    $identifier,
                    isset($_GET['depth']) ? (int) $_GET['depth'] + 1 : 2
                ),
            ];
        }

        return $mappedItem;
    }

    /**
     * Build the async fetch URL for a child menu item.
     *
     * @param int    $pageId The child item ID.
     * @param string $identifier The menu identifier.
     *
     * @return string
     */
    private function buildFetchUrl(int $pageId, string $identifier): string
    {
        $depth   = isset($_GET['depth']) ? (int) $_GET['depth'] + 1 : 2;
        $homeUrl = rtrim(esc_url(get_home_url()), '/');
        return $homeUrl
            . '/wp-json/municipio/v1/navigation/children/render'
            . '?pageId=' . $pageId
            . '&depth=' . $depth
            . '&viewPath=partials.navigation.mobile'
            . '&identifier=' . $identifier;
    }

    private function getExtendedDropdownData(string $identifier, array $data): array
    {
        if ($identifier === 'extended-dropdown-children') {
            $data['classList'][] = 'has-children';
        }

        return $data;
    }
}
