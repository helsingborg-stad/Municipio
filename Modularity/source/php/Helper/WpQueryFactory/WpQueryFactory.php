<?php

declare(strict_types=1);

namespace Modularity\Helper\WpQueryFactory;

class WpQueryFactory implements WpQueryFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(string|array $args = []): \WP_Query
    {
    //  $args = [
    //     'post_type' => ['event'],

    //     'post_status' => ['publish', 'inherit'],

    //     'meta_query' => [
    //         [
    //             'key'     => 'startDate',
    //             'value'   => '2026-10-05 09:12:20',
    //             'compare' => '>=',
    //             'type'    => 'DATETIME',
    //         ],
    //     ],

    //     'orderby' => 'meta_value',
    //     'order' => 'ASC',

    //     'posts_per_page' => 4,
    //     'paged' => 1,
    // ];

    $query = new \WP_Query($args);

    // echo '<pre>' . print_r( $query->query, true ) . '</pre>';die;
    return $query;
    }
}
