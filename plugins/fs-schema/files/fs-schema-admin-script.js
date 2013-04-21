/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, Javascript for WP-admin

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/


fs_schema_admin = {

	post_id : '0',
	
	set_integration_visible : function ( ) {
	
		switch ( jQuery ( 'input:radio[name=fs_schema_integration]:checked' ).val()) {
		
			case 'BRP':
			
				jQuery( '.fs_schema_brp' ).show( 'slow' );
				
				jQuery( '.fs_schema_profit' ).hide( 0 );
				
				break;
		
			case 'PROFIT':
			
				jQuery( '.fs_schema_brp' ).hide( 0 );
				
				jQuery( '.fs_schema_profit' ).show( 'slow' );
				
				break;
		
		};
	}, 
	
	init : function () {
	
		fs_schema_admin.set_integration_visible ( );
		
		jQuery ( 'input:radio[name=fs_schema_integration]' ).click( function() { fs_schema_admin.set_integration_visible() });
		
	},
	
	show_shortcode_help : function () {
	
		jQuery ( 'a.fs_shema_shortcode_help' ).hide();
		
		jQuery ( 'div.fs_shema_shortcode_help' ).show('slow');
	
	},
	
	brp_update_businessunitids : function () {
	
		brp_api_key =  jQuery ( 'input[name=fs_booking_bpi_api_key]' ).val();
		
		fs_schema_brp_server_url =  jQuery ( 'input[name=fs_schema_brp_server_url]' ).val();
		
		if ( brp_api_key == '' ) {
		
			alert( 'Du måste ange en API nyckel innan du kan hämta anläggningarna.' );
			
		} else {
		
			if ( fs_schema_brp_server_url == '' ) {
			
				alert( 'Du måste ange en BRP API-url innan du kan hämta anläggningarna.' );
		
			} else {
			
				jQuery( '.brp_no_businessunitsloaded' ).hide();
				
				jQuery( '.brp_businessunitsloaded' ).show();
			
				jQuery( '.brp_businessunitslist' ).html( 'Hämtar data från BRP...' )
			
				fs_schema_admin.ajax (
				
					{ action : 'get_businessunits', api_key : brp_api_key, server_url : fs_schema_brp_server_url }, function ( data ) { 
					
						jQuery( '.brp_businessunitslist' ).html(data);
						
					}
				);
			}
		}
	},
	
	ajax : function ( data, fn_success, fn_error ) {
	
		jQuery.ajax({
		
			type: "POST", url: ajaxurl, dataType: 'html', data: data, context: document.body,
			
			success: function( data) { fn_success ( data ); },
			
			error: function ( jqXHR, textStatus, errorThrown )  { if ( fn_error ) { fn_error(); } else { alert(textStatus + ' ' + errorThrown ); }}
			
		});      
     }
     
}

jQuery(document).ready(function() { fs_schema_admin.init(); });


