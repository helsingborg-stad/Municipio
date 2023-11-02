<?php

namespace Municipio\Helper;

use WP_UnitTestCase;

class WPTest extends WP_UnitTestCase
{
    public function testGetTermsReturnsArrayOfExistingTerms()
    {
        // Arrange
        $term = $this->factory()->term->create_and_get(['taxonomy' => 'category']);
        $post = $this->factory()->post->create_and_get();
        wp_set_post_terms($post->ID, [$term->term_id], 'category');

        // Act
        $result = WP::getTerms(['taxonomy' => 'category'], $post->ID);

        // Assert
        $this->assertEquals($term->term_id, $result[0]->term_id);

        // Cleanup
        wp_delete_term($term->term_id, 'category');
        wp_delete_post($post->ID, true);
    }

    public function testGetTermsReturnsEmptyArrayWhenNoTerms()
    {
        // Arrange, Act
        $terms = WP::getTerms(['taxonomy' => 'nonexistent_taxonomy']);
        // Assert
        $this->assertEmpty($terms);
    }
}
