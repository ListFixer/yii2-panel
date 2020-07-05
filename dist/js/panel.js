jQuery( document ).ready( function( ) {
	jQuery( '.lf-links' ).on( 'click', 'li, tr', function( event ) {
		var action = jQuery( this ).closest( "[data-action]" ).attr( 'data-action' );
		var key = jQuery( this ).attr( 'data-key' );
		var parm = jQuery( this ).closest( "[data-parm]" ).attr( 'data-parm' );
		if ( action && key )
			window.location.href = action + '/' + key + ( parm ? '?parm=' + parm : '' );
	} );
} );
