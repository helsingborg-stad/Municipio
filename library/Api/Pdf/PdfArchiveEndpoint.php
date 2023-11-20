<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;

class PdfArchiveEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'pdf/v2';
    // private const ROUTE = '/id=(?P<id>\d+(?:,\d+)*)';
    private const ROUTE = '/(?P<postType>[a-zA-Z]+)/(?P<query>.*)';
    
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods' => 'GET',
            'callback' => array($this, 'handleRequest'),
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $query = $request->get_param('query');
        $postType = $request->get_param('postType');
        echo '<pre>' . print_r( $query, true ) . '</pre>';die;
    }
}
