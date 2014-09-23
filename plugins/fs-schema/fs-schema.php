<?php
/*/////////////////////////////////////////////////////////////////

	Plugin Name: FS SCHEMA
	Plugin URI: http://klasehnemark.com
	Description: Detta plugin ger funktionalitet att integrera både BRP och PROFITs bokningssystem på en Friskis & Svettis Wordpress sajt. 
	Author: Klas Ehnemark
	Version: 1.0.5
	Author URI: http://klasehnemark.com
	
	Copyright (C) 2013-2014 Klas Ehnemark (http://klasehnemark.com)
	This program is not free software.


//////////////////////////////////////////////////////////////////*/


ob_start ();



////////////////////////////////////////////////////////////////////////////////
//
// REQUIRE FILES
//
////////////////////////////////////////////////////////////////////////////////

require_once ( "files/fs-schema-admin.php" );

require_once ( "files/fs-schema-ajax.php" );

require_once ( "files/fs-schema-data.php" );

require_once ( "files/fs-schema-public.php" );

require_once ( "files/fs-schema-shortcodes.php" );

require_once ( "files/fs-schema-static.php" );

require_once ( "files/fs-schema-brp.php" );

require_once ( "files/fs-schema-profit.php" );



////////////////////////////////////////////////////////////////////////////////
//
// ERROR HANDLING ACTIVATING PLUGIN
//
////////////////////////////////////////////////////////////////////////////////

add_action ( 'activated_plugin', 'fs_schema_save_error' );

function fs_schema_save_error () {

    update_option ( 'fs_schema_plugin_error',  ob_get_contents () );
    
}

update_option ( 'fs_schema_plugin_error',  '' );




////////////////////////////////////////////////////////////////////////////////
//
// MAIN CLASS
//
////////////////////////////////////////////////////////////////////////////////


class fs_schema extends fs_schema_static {

	public $admin;
	
	public $ajax;
	
	public $data;
	
	public $public;
	
	public $shortcodes;
	

	////////////////////////////////////////////////////////////////////////////////
	//
	// INITIALIZE OBJECT
	//
	////////////////////////////////////////////////////////////////////////////////

	public function __construct() {
	

		///////////////////////////////////////////////////////////////////////////
		//
		// Start initializing containing objects
		//
		///////////////////////////////////////////////////////////////////////////

		$this->admin 		= new fs_schema_admin ( plugin_basename(__FILE__) );
		
		$this->ajax 		= new fs_schema_ajax ();
		
		$this->data 		= new fs_schema_data ();
		
		$this->public 		= new fs_schema_public ();
		
		$this->shortcodes 	= new fs_schema_shortcodes ();
		


		///////////////////////////////////////////////////////////////////////////
		//
		// Register hooks for activate and deactivate plugin
		//
		///////////////////////////////////////////////////////////////////////////
		
		register_activation_hook 	(__FILE__, 	array( &$this, 'on_activate_plugin' ));
		
		register_deactivation_hook 	( __FILE__, 	array( &$this, 'on_deactivate_plugin' ));
		
		register_uninstall_hook 		( __FILE__, 	'fs_schema::on_uninstall_plugin' );



	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// ACTIVATING AND DEACTIVATING PLUGIN FUNCTION
	//
	////////////////////////////////////////////////////////////////////////////////
	
	public function on_activate_plugin() {
	
	}
	
	public function on_deactivate_plugin() {
	
	}
	
	public static function on_uninstall_plugin() {
	
	}	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// FOR DEBUGGING
	//
	////////////////////////////////////////////////////////////////////////////////
		
	public static function debug ( $what, $die = false ) {
	
		$output = '<pre>' . print_r ( $what, true ) . '</pre>';
		
		if ( $die === true ) wp_die ( $output );
		else echo $output;
	
	}
	

	
} //End Class

$fs_schema = new fs_schema();

echo get_option( 'fs_schema_plugin_error' );

?>