<?php

namespace Municipio\Api\Navigation;

use Municipio\Api\RestApiEndpoint;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use WP_REST_Request;
use WP_REST_Response;
use WpService\Contracts\GetPostType;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Helper\GetGlobal;

class Children extends RestApiEndpoint
{
    private const NAMESPACE = 'municipio/v1';
    private const ROUTE     = '/navigation/children';

    public function __construct(private MenuItemsDecoratorInterface $menuComplementer)
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
        $items = [];
        if (isset($params['pageId']) && is_numeric($params['pageId'])) {
            $parentId   = $params['pageId'];
            
            if (isset($parentId)) {
                $identifier = !empty($params['identifier']) ? $params['identifier'] : '';
                $localWpdb = GetGlobal::getGlobal('wpdb');

                $menuConfig = new MenuConfig(
                    $identifier,
                    '',
                    $parentId,
                    '',
                    $localWpdb,
                    true,
                    true,
                    false,
                    false
                );

                $items = $this->menuComplementer->decorate([], $menuConfig);
            }
        }

        return rest_ensure_response($items);
    }
}
