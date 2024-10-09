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
            'permission_callback' => '__return_true'
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $params = $request->get_params();

        if (isset($params['pageId']) && is_numeric($params['pageId'])) {
            $parentId   = !empty($params['pageId']) ? $params['pageId'] : null;
            $viewPath   = !empty($params['viewPath']) ? $params['viewPath'] : false;
            $identifier = !empty($params['identifier']) ? $params['identifier'] : '';
            $depth      = !empty($params['depth']) ? $params['depth'] : '0';
            $lang       = TranslatedLabels::getLang();

            if (!empty($parentId)) {
                $menuConfig = new MenuConfig(
                    $identifier,
                    '',
                    false,
                    false,
                    $parentId
                );

                $this->menuBuilder->setConfig($menuConfig);
                $this->menuDirector->setBuilder($this->menuBuilder);
                $this->menuDirector->buildPageTreeMenu();
                $menuItems = $this->menuBuilder->getMenu()->getMenuItems();

                return rest_ensure_response(array(
                    'parentId' => $parentId,
                    'viewPath' => $viewPath ?: 'partials.navigation.mobile',
                    'markup'   => render_blade_view($viewPath ?: 'partials.navigation.mobile', [
                        'menuItems' => $menuItems,
                        'homeUrl'   => esc_url(get_home_url()),
                        'depth'     => $depth,
                        'lang'      => $lang
                    ])
                ));
            }
        }

        return rest_ensure_response([]);
    }
}
