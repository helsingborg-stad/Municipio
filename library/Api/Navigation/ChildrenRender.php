<?php

namespace Municipio\Api\Navigation;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Helper\TranslatedLabels;
use Municipio\Controller\Navigation\Config\NewMenuConfig;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\MenuBuilder;
use Municipio\Controller\Navigation\MenuDirector;
use WpService\Contracts\GetPostType;
use WpService\WpService;
use AcfService\AcfService;

class ChildrenRender extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = '/navigation/children/render';

    public function __construct(private GetPostType $wpService, private AcfService $acfService)
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
                $config = new NewMenuConfig(
                    $identifier,
                    '',
                    false,
                    false,
                    $parentId
                );

                $director = new MenuDirector();
                $builder = new MenuBuilder($config, $this->acfService, $this->wpService);
                $director->setBuilder($builder);
                $director->buildPageTreeMenu();
                $menuItems = $builder->getMenu()->getMenuItems();

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
