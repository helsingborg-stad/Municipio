<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Api\Pdf\PdfHelper;

class PdfArchiveEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'pdf/v2';
    private const ROUTE = '/(?P<postType>[a-zA-Z]+)';
    
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods' => 'GET',
            'callback' => array($this, 'handleRequest'),
        ));
    }

    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $pdfHelper = new PdfHelper();
        $postType = $request->get_param('postType');
        $queryParams = $request->get_query_params();

        $posts = $this->getArchivePosts($postType, $queryParams);
        
        if (!empty($posts)) {
            $cover = $pdfHelper->getCoverFieldsForPostType($postType);
            $pdf = new \Municipio\Api\Pdf\CreatePdf();
            return $pdf->renderView($posts, $cover);
        }

        return new WP_REST_Response('No valid posts', 200);
    }

    private function getArchivePosts($postType = false, $queryParams = false) {
        $posts = [];
        $args = [
            'post_type' => $postType,
            'tax_query' => [],
            'posts_per_page' => -1
        ];
        foreach ($queryParams as $key => $values) {
            $args['tax_query'][] = [
                'taxonomy' => $key,
                'field' => 'slug',
                'terms' => $values
            ];
        }
        
        $query = new \WP_Query($args);
        
        if (!empty($query->posts)) {
            foreach($query->posts as &$post) {
                $post = \Municipio\Helper\Post::preparePostObject($post);
                array_push($posts, $post);
            }
        }
        
        return $posts;
    }
}
