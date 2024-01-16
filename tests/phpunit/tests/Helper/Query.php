<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\Query;
use Mockery;
use WP_Mock;

/**
 * Class QueryTest
 */
class QueryTest extends TestCase
{
    /**
     * @testdox getPaginationData returns array of query data.
    */
    public function testGetPaginationData()
    {
        $wpQueryMock                = Mockery::mock('WP_Query');
        $wpQueryMock->query         = ['post_type' => 'test', 'paged' => "2"];
        $wpQueryMock->post_count    = "test";
        $wpQueryMock->found_posts   = "test";
        $wpQueryMock->max_num_pages = "test";


        $GLOBALS['wp_query'] = $wpQueryMock;

        // When
        $result = Query::getPaginationData();

        // Then
        $this->assertEquals(2, $result['pageIndex']);
        $this->assertEquals('test', $result['postType']);
        $this->assertEquals('test', $result['postCount']);
        $this->assertEquals('test', $result['postTotal']);
        $this->assertEquals('test', $result['pageTotal']);
    }

    /**
     * @testdox getTaxQueryTerms returns false if no tax query.
    */
    public function testGetTaxQueryTermsReturnsFalseIfNoTaxQuery()
    {
        // Given
        WP_Mock::userFunction('get_query_var', [
            'return' => false,
        ]);

        // When
        $result = Query::getTaxQueryTerms();

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getTaxQueryTerms returns false if no taxonomies found.
    */
    public function testGetTaxQueryTermsReturnsFalseIfNoTaxonomiesFound()
    {
        // Given
        WP_Mock::userFunction('get_query_var', [
            'return' => [
                'relation' => 'OR',
                'test'     => 'test'
            ]
        ]);

        // When
        $result = Query::getTaxQueryTerms();

        // Then
        $this->assertFalse($result);
    }

       /**
     * @testdox getTaxQueryTerms returns false if no taxonomies found.
    */
    public function testGetTaxQueryTermsReturnsFalseIfNoTermsFound()
    {
        // Given
        WP_Mock::userFunction('get_query_var', [
            'return' => [
                'relation' => 'OR',
                [
                    'taxonomy' => 'test1',
                    'field'    => 'slug',
                    'terms'    => [
                    ]
                ],
                'operator' => 'IN',
            ]
        ]);

        WP_Mock::userFunction('get_term_by', [
            'return' => false
        ]);

        // When
        $result = Query::getTaxQueryTerms();

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getTaxQueryTerms returns false if no taxonomies found.
    */
    public function testGetTaxQueryTermsReturnsTaxonomies()
    {
        // Given
        WP_Mock::userFunction('get_query_var', [
            'return' => [
                'relation' => 'OR',
                [
                    'taxonomy' => 'test1',
                    'field'    => 'slug',
                    'terms'    => [
                        'term1',
                        'term2'
                    ]
                ],
                'operator' => 'IN',
                [
                    'taxonomy' => 'test2',
                    'field'    => 'slug',
                    'terms'    => [
                        'term1',
                        'term2'
                    ]
                ]
            ]
        ]);

        WP_Mock::userFunction('get_term_by', [
            'return' => Mockery::mock('WP_Term')
        ]);

        // When
        $result = Query::getTaxQueryTerms();

        // Then
        $this->assertCount(4, $result);
        $this->assertInstanceOf('WP_Term', $result[0]);
        $this->assertInstanceOf('WP_Term', $result[1]);
        $this->assertInstanceOf('WP_Term', $result[2]);
        $this->assertInstanceOf('WP_Term', $result[3]);
    }
}
