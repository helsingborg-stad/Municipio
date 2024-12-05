<?php

namespace Municipio\Api\Pdf;

use Municipio\Api\RestApiEndpoint;
use WP_REST_Request;
use WP_REST_Response;
use Municipio\Api\Pdf\PdfHelper as PDFHelper;
use Municipio\Helper\FileConverters\WoffConverter as WoffConverterHelper;

/**
 * Class PdfArchiveEndpoint
 *
 * PDF REST API endpoint for handling PDF generation based on archive parameters.
 *
 * @package Municipio\Api\Pdf
 */
class PdfArchiveEndpoint extends RestApiEndpoint
{
    private const NAMESPACE = 'pdf/v1';
    private const ROUTE     = '/(?P<postType>[a-zA-Z-_]+)';

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
            'permission_callback' => '__return_true',
            'args'                => [
                'after'  => [
                    'required'          => false,
                    'validate_callback' => fn ($param) => is_string($param) && strtotime($param),
                ],
                'before' => [
                    'required'          => false,
                    'validate_callback' => fn ($param) => is_string($param) && strtotime($param),
                ]
            ],
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
        $postType = $request->get_param('postType');

        if ($this->isPublicPostType($postType)) {
            $pdfHelper   = new PDFHelper();
            $woffHelper  = new WoffConverterHelper();
            $queryParams = $request->get_query_params();

            $posts = $this->handleArchivePosts($postType, $queryParams);

            if (!empty($posts)) {
                $cover = $pdfHelper->getCoverFieldsForPostType($postType);
                $pdf   = new \Municipio\Api\Pdf\CreatePdf($pdfHelper, $woffHelper);
                $html  = $pdf->getHtmlFromView($posts, $cover);
                $pdf->renderPdf($html, $postType);
                return new WP_REST_Response(null, 200);
            }

            return new WP_REST_Response(
                null,
                302,
                [
                    'Location' => site_url('/404'),
                ]
            );
        }

        return new WP_REST_Response(
            null,
            302,
            [
                'Location' => site_url('/404'),
            ]
        );
    }

    /**
     * Retrieves posts for the archive based on the given parameters.
     *
     * @param string $postType    The post type.
     * @param array  $queryParams The query parameters.
     *
     * @return array The retrieved posts.
     */
    private function handleArchivePosts($postType = '', $queryParams = false)
    {
        $posts = $this->getPostsFromArchiveQuery($postType, $queryParams);

        $postsWithTerms    = [];
        $postsWithoutTerms = [];
        if (!empty($posts)) {
            $sortByTerm = get_field('field_pdf_sort_posts_by_term', 'option');
            $data       = null;

            if (!empty($sortByTerm)) {
                $data = ['taxonomiesToDisplay' => get_object_taxonomies($postType)];
            }

            foreach ($posts as $post) {
                $post = \Municipio\Helper\Post::preparePostObject($post, $data);

                if (!empty($post->id) && empty(get_field('post_single_show_featured_image', $post->id))) {
                    $post->images = false;
                }
                if (!empty($sortByTerm) && !empty($post->termsUnlinked[0]['label'])) {
                    $postsWithTerms[$post->termsUnlinked[0]['label']][] = $post;
                } else {
                    $postsWithoutTerms[] = $post;
                }
            }

            // sort based on the term name.
            ksort($postsWithTerms);

            if (!empty($postsWithoutTerms)) {
                $postsWithoutTermsName                                              = get_field('field_pdf_sort_posts_without_term_label', 'option');
                $postsWithTerms[$postsWithoutTermsName ?? __('Other', 'Municipio')] = $postsWithoutTerms;
            }
        }

        return $postsWithTerms;
    }

    /**
     * Retrieves posts from the archive query based on the given parameters.
     *
     * @param string $postType    The post type.
     * @param array  $queryParams The query parameters.
     *
     * @return array The retrieved posts.
     */
    private function getPostsFromArchiveQuery($postType = '', $queryParams = false)
    {
        $orderBy   = get_theme_mod('archive_' . $postType . '_order_by', 'post_date');
        $order     = get_theme_mod('archive_' . $postType . '_order_direction');
        $facetting = empty(get_theme_mod('archive_' . $postType . '_filter_type')) ? 'IN' : 'AND';

        $args = [
            'post_type'      => $postType,
            'tax_query'      => [],
            'date_query'     => [
                'inclusive' => false,
            ],
            'posts_per_page' => -1,
            'orderby'        => !empty($orderBy) ? $orderBy : 'post_date',
            'order'          => !empty($order) ? $order : 'desc'
        ];

        if (!empty($queryParams) && is_array($queryParams)) {
            if (!empty($queryParams['after'])) {
                $args['date_query']['after'] = $queryParams['after'];
                unset($queryParams['after']);
            }

            if (!empty($queryParams['before'])) {
                $args['date_query']['before'] = $queryParams['before'];
                unset($queryParams['before']);
            }

            if (isset($args['date_query']['after']) && isset($args['date_query']['before'])) {
                $args['date_query']['compare'] = 'BETWEEN';
            }

            if (isset($queryParams['s'])) {
                $args['s'] = $queryParams['s'];
                unset($queryParams['s']);
            }

            foreach ($queryParams as $key => $values) {
                if (empty($values)) {
                    continue;
                } else {
                    $args['tax_query'][] = [
                        'taxonomy' => $key,
                        'field'    => 'slug',
                        'terms'    => $values,
                        'operator' => $facetting
                    ];
                }
            }
        }

        $query = new \WP_Query($args);

        return $query->posts;
    }

    /**
     * Handles date and search filtering for the archive.
     *
     * @param array $queryParams The query parameters.
     *
     * @return array The modified query parameters.
     */
    private function handleDateAndSearchFiltering(array $queryParams)
    {
        return $queryParams;
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
