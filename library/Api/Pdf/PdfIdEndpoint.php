<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;

class PdfIdEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'pdf/v2';
    // private const ROUTE = '/id=(?P<id>\d+(?:,\d+)*)';
    private const ROUTE = '/id=(?P<id>[\d,]+)';
    
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods' => 'GET',
            'callback' => array($this, 'handleRequest'),
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $ids = $request->get_param('id');
        if (!empty($ids) && is_string($ids)) {
            $idArr = explode(',', $ids);
            $pdf = new \Municipio\Api\Pdf\CreatePdf();
            return $pdf->renderView($idArr);
        }
    }
}
