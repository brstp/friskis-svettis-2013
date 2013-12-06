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
			'typ'			=> 'vecka', 		// vecka, dag
			'anlaggning'		=> '',			// id, eller kommaseparerad id
			'datum'			=> '',  			// format: YYYY-MM-DD
			'bokning'			=> '1',
			'visavyknapp'		=> '1',
			'dagsbredd'		=> '130'
		);
		
		$r = wp_parse_args( $attr, $defaults );

		global $fs_schema;
		
		$args	= array (
			'type'			=> $r['typ'] == 'dag' ? 'day' : 'week',
			'facility'		=> $r['anlaggning'],
			'date'			=> $r['datum'], 
			'day_width'		=> $r['dagsbredd'],
			'booking'			=> $r['bokning'],
			'enableweek'		=> $r['visavyknapp'] == '0' ? false : true,
			'enableday'		=> $r['visavyknapp'] == '0' ? false : true,
		);

		return  $fs_schema->public->render_schema ( $args );
		
	}

}

?>