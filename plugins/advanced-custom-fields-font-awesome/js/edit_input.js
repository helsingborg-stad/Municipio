(function($){
	
	var fa_initialized = false;

	function initialize_field( $el ) {

		if ( $el.parent('.row-clone').length === 0 && $el.parents('.clones').length === 0 ) {
			$( 'select.fa-select2-field', $el ).each( function() {
				$(this).select2({
					width : '100%'
				});
				update_preview( this, $(this).val() );
			});
		}

		$( 'select.fa-select2-field' ).on( 'select2-selecting', function( object ) {
			update_preview( this, object.val );
		});

		$( 'select.fa-select2-field' ).on( 'select2-highlight', function( object ) {
			update_preview( this, object.val );
		});

		$( 'select.fa-select2-field' ).on( 'select2-close', function( object ) {
			update_preview( this, $(this).val() );
		});

	}
	
	function update_preview( element, selected ) {
		var parent = $(element).parent();
		$( '.fa-live-preview', parent ).html( '<i class="fa ' + selected + '"></i>' );
	}

	if( typeof acf.add_action !== 'undefined' ) {

		acf.add_action('ready append', function( $el ){

			// search $el for fields of type 'FIELD_NAME'
			acf.get_fields({ type : 'font-awesome'}, $el).each(function(){

				initialize_field( $(this) );

			});

		});

	} else {

		$(document).live('acf/setup_fields', function(e, postbox){

			$(postbox).find('.field[data-field_type="font-awesome"], .sub_field[data-field_type="font-awesome"]').each(function(){
				initialize_field( $(this) );
			});

		});

	}

})(jQuery);