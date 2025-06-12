<?php

namespace Municipio\Helper;

class CurrentPostId {
	public static $pageId = 0;

	public static function get() {
		// Return cached value if already set
		if ( ! empty( self::$pageId ) ) {
			return self::$pageId;
		}

		// Page for post type archive mapping result
		if ( is_post_type_archive() ) {
			$queriedObject = get_queried_object();
			$postType      = get_post_type() ?: ( is_object( $queriedObject ) ? $queriedObject->name : null );

			if ( $pageId = get_option( 'page_for_' . $postType ) ) {
				return self::$pageId = (int) $pageId;
			}
		}

		// Get the queried page
		if ( $queriedObjectId = get_queried_object_id() ) {
			return self::$pageId = $queriedObjectId;
		}

		// Return page for front page (fallback)
		if ( $frontPageId = get_option( 'page_on_front' ) ) {
			return self::$pageId = $frontPageId;
		}

		// Return page for blog (fallback)
		if ( $blogPageId = get_option( 'page_for_posts' ) ) {
			return self::$pageId = $blogPageId;
		}

		// If none of the above, set and return 0
		return self::$pageId = 0;
	}
}
