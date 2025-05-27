<?php

namespace Municipio\Api\Navigation;

use Municipio\Api\RestApiEndpoint;
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

        $viewPath = empty($params['viewPath']) ? 'partials.navigation.mobile' : $params['viewPath'];
        $depth    = !empty($params['depth']) ? $params['depth'] : 1;
        $lang     = TranslatedLabels::getLang();


        $menuConfig = new MenuConfig(
            $params['identifier'],
            '',
            false,
            false,
            $params['pageId']
        );

        $this->menuBuilder->setConfig($menuConfig);
        $this->menuDirector->setBuilder($this->menuBuilder);
        $this->menuDirector->buildPageTreeMenu();
        $menuItems = $this->menuBuilder->getMenu()->getMenu()['items'];

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
            'parentId' => $params['pageId'],
            'viewPath' => $viewPath,
            'markup'   => $markup
        ));
    }
}
