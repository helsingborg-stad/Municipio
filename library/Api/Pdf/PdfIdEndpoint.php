<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Api\Pdf\PdfHelper;

class PdfIdEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'pdf/v1';
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
        $pdfHelper = new PdfHelper();
        $idString = $request->get_param('id');

        if (!empty($idString) && is_string($idString)) {
            $idArr = explode(',', $idString);
            [$posts, $postTypes] = $this->getPostsById($idArr);
            $cover = $pdfHelper->getCover($postTypes);
        }
    
        if (!empty($posts)) {
            $pdf = new \Municipio\Api\Pdf\CreatePdf();
            return $pdf->renderView($posts, $cover, !empty($posts[0]->postName) ? $posts[0]->postName : 'page-pdf');
        }

        return new WP_REST_Response('No valid posts', 200);
    }

    private function getPostsById(array $ids) {
        $posts = [];
        $postTypes = [];
        if (!empty($ids) && is_array($ids)) {
            foreach ($ids as $id) {
                $post = get_post($id);
                if (!empty($post->post_status) && $post->post_status == 'publish') {
                    $post = \Municipio\Helper\Post::preparePostObject($post);
                    if (!empty($post->postType)) {
                        $postTypes[$post->postType] = $post->postType;
                    }
                    array_push($posts, $post);
                }
            }
        }
        return [$posts, $postTypes];
    }
}
