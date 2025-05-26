<?php

namespace Municipio\Api\Navigation;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Helper\TranslatedLabels;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;

class ChildrenRender extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = '/navigation/children/render';

    public function __construct(private MenuBuilderInterface $menuBuilder, private MenuDirector $menuDirector)
    {
    }

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
                    'sanitize_callback' => fn($input) => empty($input) ? false : sanitize_text_field($input),
                ),
                'depth'      => array(
                    'required'          => false,
                    'validate_callback' => fn($input) => is_numeric($input),
                    'sanitize_callback' => fn($input) => (int)$input,
                ),
                'identifier' => array(
                    'required'          => false,
                    'sanitize_callback' => fn($input) => sanitize_text_field($input),
                ),
            ),
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $params = $request->get_params();

        $viewPath = !empty($params['viewPath']) ? $params['viewPath'] : false;
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

        return rest_ensure_response(array(
            'parentId' => $params['pageId'],
            'viewPath' => $viewPath ?: 'partials.navigation.mobile',
            'markup'   => render_blade_view($viewPath ?: 'partials.navigation.mobile', [
                'menuItems' => $menuItems,
                'homeUrl'   => esc_url(get_home_url()),
                'depth'     => $depth,
                'lang'      => $lang,
                'classList' => []
            ])
        ));

        return rest_ensure_response([]);
    }
}
