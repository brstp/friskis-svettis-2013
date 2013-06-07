<?php
/*/////////////////////////////////////////////////////////////////

	FS SCHEMA, PHP Class - SHORTCODES

	Copyright (C) 2013 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.

//////////////////////////////////////////////////////////////////*/


class fs_schema_shortcodes {

	// Public variables
	public $method;


	////////////////////////////////////////////////////////////////////////////////
	//
	// CONSTRUCTOR
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct ( $args = null ) {

		add_shortcode('fs-schema', 		array ( $this, 'schema' ));
		
	}
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// SCHEMA
	//
	////////////////////////////////////////////////////////////////////////////////
	
	public function schema ( $attr, $content = null ) {
	
		$defaults = array(
			'typ'			=> 'vecka', 		// vecka, pass
			'anlaggning'		=> '',			// id, eller kommaseparerad id
			'datum'			=> '',  	// format: YYYY-MM-DD
			'bokning'			=> '1'
		);
		
		$r = wp_parse_args( $attr, $defaults );
		
		global $fs_schema;
		
		//return '<pre>' . print_r ($fs_schema->data->book_activity ( 'klas@ehnemark.com', 'gurka7394', '18646' ), true ) . '</pre>';
		
		return  $fs_schema->public->render_schema ( $r );
		
	}

}

?>