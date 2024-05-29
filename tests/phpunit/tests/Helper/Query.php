<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\Query;
use Mockery;
use WP_Mock;

/**
 * Class QueryTest
 * @group wp_mock
 */
class QueryTest extends TestCase
{
    /**
     * @testdox getPaginationData returns array of query data.
    */
    public function testGetPaginationData()
    {
        $wpQueryMock                = Mockery::mock('WP_Query');
        $wpQueryMock->query         = ['post_type' => 'testPostType', 'paged' => "2"];
        $wpQueryMock->post_count    = "testPostCount";
        $wpQueryMock->found_posts   = "testPostTotal";
        $wpQueryMock->max_num_pages = "testPageTotal";


        $GLOBALS['wp_query'] = $wpQueryMock;

        // When
        $result = Query::getPaginationData();

        // Then
        $this->assertEquals(2, $result['pageIndex']);
        $this->assertEquals('testPostType', $result['postType']);
        $this->assertEquals('testPostCount', $result['postCount']);
        $this->assertEquals('testPostTotal', $result['postTotal']);
        $this->assertEquals('testPageTotal', $result['pageTotal']);
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
                    ]
                ],
                'operator' => 'IN',
                [
                    'taxonomy' => 'test2',
                    'field'    => 'slug',
                    'terms'    => [
                        'term2',
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
        $this->assertCount(2, $result);
        $this->assertInstanceOf('WP_Term', $result[0]);
        $this->assertInstanceOf('WP_Term', $result[1]);
    }
}
