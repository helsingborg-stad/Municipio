<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Api\Pdf\PdfHelper as PDFHelper;

class PdfArchiveEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'pdf/v1';
    private const ROUTE = '/(?P<postType>[a-zA-Z]+)';
    
    /**
     * Handles the registration of the REST route.
     *
     * @return bool Whether the REST route registration was successful.
     */
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods' => 'GET',
            'callback' => array($this, 'handleRequest'),
        ));
    }

    /**
     * Handles the REST API request.
     *
     * @param WP_REST_Request $request The REST API request object.
     *
     * @return WP_REST_Response The REST API response object.
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $pdfHelper = new PDFHelper();
        $postType = $request->get_param('postType');
        $queryParams = $request->get_query_params();

        $posts = $this->getArchivePosts($postType, $queryParams);
        
        if (!empty($posts)) {
            $cover = $pdfHelper->getCoverFieldsForPostType($postType);
            $pdf = new \Municipio\Api\Pdf\CreatePdf();
            return $pdf->renderView($posts, $cover, $postType);
        }

        return new WP_REST_Response('No valid posts', 200);
    }

    /**
     * Retrieves posts for the archive based on the given parameters.
     *
     * @param string $postType    The post type.
     * @param array  $queryParams The query parameters.
     *
     * @return array The retrieved posts.
     */
    private function getArchivePosts($postType = false, $queryParams = false) {
        $orderBy = get_theme_mod('archive_' . $postType . '_order_by', 'post_date');
        $order = get_theme_mod('archive_' . $postType . '_order_direction');
        $facetting = empty(get_theme_mod('archive_' . $postType . '_filter_type')) ? 'IN' : 'AND';

        $posts = [];
        $args = [
            'post_type' => $postType,
            'tax_query' => [],
            'date_query' => [
                'inclusive' => true,
            ],
            'posts_per_page' => -1,
            'order_by' => !empty($orderBy) ? $orderBy : 'post_date',
            'order' => !empty($order) ? $order : 'desc'
        ];

        foreach ($queryParams as $key => $values) {
            if (empty($values)) {
                //Do nothing for empty values
            } elseif ($key == 'from' || $key == 'to') {
                if ($key == 'from') {
                    $args['date_query']['after'] = $values;
                }

                if ($key == 'to') {
                    $args['date_query']['before'] = $values;
                    $args['date_query']['compare'] = '<=';
                }

                //if both after and before are set we compare dates between the two
                if (isset($args['date_query']['after']) && isset($args['date_query']['before'])) {
                    $args['date_query']['compare'] = 'BETWEEN';
                }
            } elseif ($key == 's') {
                $args['s'] = $values;
            } else {
                $args['tax_query'][] = [
                    'taxonomy' => $key,
                    'field' => 'slug',
                    'terms' => $values,
                    'operator' => $facetting
                ];
            }
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
