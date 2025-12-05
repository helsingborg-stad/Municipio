<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Api\Pdf\PdfHelper as PDFHelper;
use Municipio\Helper\FileConverters\WoffConverter as WoffConverterHelper;
use WP_Post;

/**
 * Class PdfIdEndpoint
 *
 * PDF REST API endpoint for handling PDF generation based on post IDs.
 *
 * @package Municipio\Api\Pdf
 */
class PdfIdEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'pdf/v1';
    private const ROUTE     = '/id=(?P<id>[\d,-]+)';

    /**
     * Handles the registration of the REST route.
     *
     * @return bool Whether the REST route registration was successful.
     */
    public function handleRegisterRestRoute(): bool
    {
        return register_rest_route(self::NAMESPACE, self::ROUTE, array(
            'methods'             => 'GET',
            'callback'            => array($this, 'handleRequest'),
            'permission_callback' => '__return_true'
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
        $pdfHelper  = new PDFHelper();
        $woffHelper = new WoffConverterHelper();
        $idString   = $request->get_param('id');

        if (!empty($idString) && is_string($idString)) {
            $idArr               = explode(',', $idString);
            [$posts, $postTypes] = $this->getPostsById($idArr);
            $cover               = $pdfHelper->getCover($postTypes);

            if (!empty($posts)) {
                $fileName = (string)(function () use ($posts) {
                    if (!empty($posts[0]->postName)) {
                        return $posts[0]->postName;
                    }
                    return 'page-pdf';
                })();
                $pdf      = new \Municipio\Api\Pdf\CreatePdf($pdfHelper, $woffHelper);
                $html     = $pdf->getHtmlFromView($posts, $cover);
                $pdf->renderPdf($html, $fileName);
            }
            return new WP_REST_Response(null, 200);
        }

        return new WP_REST_Response(
            null,
            303,
            [
                'Location' => site_url('/404'),
            ]
        );
    }

    /**
     * Retrieves posts based on the provided IDs.
     *
     * @param array $ids Array of post IDs.
     *
     * @return array Array containing posts and associated post types.
     */
    private function getPostsById(array $ids): array
    {
        $posts     = [];
        $postTypes = [];

        if (!empty($ids) && is_array($ids)) {
            foreach ($ids as $id) {
                $id   = trim($id);
                $post = get_post($id);
                if ($this->shouldRenderPost($post)) {
                    $post = \Municipio\Helper\Post::preparePostObject($post);

                    if (!empty($post->id) && empty(get_field('post_single_show_featured_image', $post->id))) {
                        $post->images = false;
                    }

                    if (!empty($post->postType)) {
                        $postTypes[$post->postType] = $post->postType;
                    }

                    array_push($posts, $post);
                }
            }
        }
        return [[$posts], $postTypes];
    }

    /**
     * Check if a post should be rendered.
     *
     * @param WP_Post $post WordPress post object.
     * @return bool Whether the post should be rendered.
     */
    private function shouldRenderPost($post): bool
    {

        if (empty($post->post_status)) {
            return false;
        }

        if (empty($post->post_type)) {
            return false;
        }

        if ($post->post_status == 'publish' && $this->isPublicPostType($post->post_type)) {
            return true;
        }

        return false;
    }

    /** Check if a post type is public.
     *
     * @param string $postType Post type to check.
     * @return bool Whether the post type is public.
     */
    private function isPublicPostType($postType): bool
    {
        $publicPostTypes = get_post_types(['public' => true]);
        if (in_array($postType, $publicPostTypes)) {
            return true;
        }
        return false;
    }
}
